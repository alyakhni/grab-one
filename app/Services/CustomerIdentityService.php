<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Customer;

class CustomerIdentityService
{
    public function normalizeEmail(
        ?string $email
    ): ?string {
        $email = trim(
            (string) $email
        );

        if ($email === '') {
            return null;
        }

        return mb_strtolower(
            $email
        );
    }

    public function normalizePhone(
        ?string $phone
    ): ?string {
        $phone = trim(
            (string) $phone
        );

        if ($phone === '') {
            return null;
        }

        $startsWithInternationalPrefix =
            str_starts_with(
                $phone,
                '00'
            );

        $startsWithPlus =
            str_starts_with(
                $phone,
                '+'
            );

        $digits = preg_replace(
            '/\D+/',
            '',
            $phone
        );

        if (
            ! is_string($digits)
            || $digits === ''
        ) {
            return null;
        }

        if ($startsWithInternationalPrefix) {
            return '+'.substr(
                $digits,
                2
            );
        }

        if ($startsWithPlus) {
            return '+'.$digits;
        }

        if (strlen($digits) === 7) {
            return '+501'.$digits;
        }

        if (
            strlen($digits) === 10
            && str_starts_with(
                $digits,
                '501'
            )
        ) {
            return '+'.$digits;
        }

        return $digits;
    }

    public function resolveForBooking(
        string $name,
        string $email,
        string $phone
    ): ?Customer {
        $emailNormalized =
            $this->normalizeEmail(
                $email
            );

        $phoneNormalized =
            $this->normalizePhone(
                $phone
            );

        $match = $this->match(
            $emailNormalized,
            $phoneNormalized
        );

        if (
            $match['ambiguous']
            || $match['conflict']
        ) {
            return null;
        }

        $customer =
            $match['customer'];

        if ($customer instanceof Customer) {
            $customer->update([
                'name' =>
                    trim($name),

                'email' =>
                    trim($email),

                'email_normalized' =>
                    $emailNormalized,

                'phone' =>
                    trim($phone),

                'phone_normalized' =>
                    $phoneNormalized,
            ]);

            $customer =
                $customer->fresh();
        } else {
            $customer = Customer::create([
                'name' =>
                    trim($name),

                'email' =>
                    trim($email),

                'email_normalized' =>
                    $emailNormalized,

                'phone' =>
                    trim($phone),

                'phone_normalized' =>
                    $phoneNormalized,
            ]);
        }

        $this->linkHistoricalContacts(
            $customer
        );

        return $customer;
    }

    public function findExistingForContact(
        string $email,
        string $phone
    ): ?Customer {
        $match = $this->match(
            $this->normalizeEmail(
                $email
            ),
            $this->normalizePhone(
                $phone
            )
        );

        if (
            $match['ambiguous']
            || $match['conflict']
        ) {
            return null;
        }

        return $match['customer'];
    }

    public function hasConflict(
        string $email,
        string $phone
    ): bool {
        $match = $this->match(
            $this->normalizeEmail(
                $email
            ),
            $this->normalizePhone(
                $phone
            )
        );

        return $match['conflict']
            || $match['ambiguous'];
    }

    protected function linkHistoricalContacts(
        Customer $customer
    ): void {
        $emailNormalized =
            $customer->email_normalized;

        $phoneNormalized =
            $customer->phone_normalized;

        if (
            $emailNormalized === null
            && $phoneNormalized === null
        ) {
            return;
        }

        Contact::query()
            ->whereNull(
                'customer_id'
            )
            ->select([
                'id',
                'email',
                'phone',
            ])
            ->chunkById(
                100,
                function (
                    $contacts
                ) use (
                    $customer,
                    $emailNormalized,
                    $phoneNormalized
                ): void {
                    foreach (
                        $contacts
                        as $contact
                    ) {
                        $contactEmail =
                            $this->normalizeEmail(
                                $contact->email
                            );

                        $contactPhone =
                            $this->normalizePhone(
                                $contact->phone
                            );

                        $matchesCustomer =
                            (
                                $emailNormalized !== null
                                && $contactEmail
                                    === $emailNormalized
                            )
                            || (
                                $phoneNormalized !== null
                                && $contactPhone
                                    === $phoneNormalized
                            );

                        if (! $matchesCustomer) {
                            continue;
                        }

                        $resolvedCustomer =
                            $this->findExistingForContact(
                                $contact->email,
                                $contact->phone
                            );

                        if (
                            ! $resolvedCustomer
                            || $resolvedCustomer->id
                                !== $customer->id
                        ) {
                            continue;
                        }

                        Contact::query()
                            ->whereKey(
                                $contact->id
                            )
                            ->whereNull(
                                'customer_id'
                            )
                            ->update([
                                'customer_id' =>
                                    $customer->id,
                            ]);
                    }
                }
            );
    }

    protected function match(
        ?string $emailNormalized,
        ?string $phoneNormalized
    ): array {
        $emailMatch =
            $this->uniqueMatch(
                'email_normalized',
                $emailNormalized
            );

        $phoneMatch =
            $this->uniqueMatch(
                'phone_normalized',
                $phoneNormalized
            );

        if (
            $emailMatch['ambiguous']
            || $phoneMatch['ambiguous']
        ) {
            return [
                'customer' => null,
                'conflict' => false,
                'ambiguous' => true,
            ];
        }

        $emailCustomer =
            $emailMatch['customer'];

        $phoneCustomer =
            $phoneMatch['customer'];

        if (
            $emailCustomer instanceof Customer
            && $phoneCustomer instanceof Customer
            && $emailCustomer->id
                !== $phoneCustomer->id
        ) {
            return [
                'customer' => null,
                'conflict' => true,
                'ambiguous' => false,
            ];
        }

        return [
            'customer' =>
                $emailCustomer
                ?? $phoneCustomer,

            'conflict' => false,
            'ambiguous' => false,
        ];
    }

    protected function uniqueMatch(
        string $field,
        ?string $value
    ): array {
        if ($value === null) {
            return [
                'customer' => null,
                'ambiguous' => false,
            ];
        }

        $customers = Customer::query()
            ->where(
                $field,
                $value
            )
            ->limit(2)
            ->get();

        if ($customers->count() > 1) {
            return [
                'customer' => null,
                'ambiguous' => true,
            ];
        }

        return [
            'customer' =>
                $customers->first(),

            'ambiguous' => false,
        ];
    }
}