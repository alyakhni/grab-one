<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingCartAssignmentService
{
    public function __construct(
        private FleetAvailabilityService $availability
    ) {
    }

    public function confirm(
        Booking $booking,
        array $cartIdsByType
    ): Booking {
        $normalizedAssignments =
            $this->normalizeAssignments(
                $cartIdsByType
            );

        $allCartIds = collect(
            $normalizedAssignments
        )
            ->flatten()
            ->map(
                fn ($id): int =>
                    (int) $id
            )
            ->filter(
                fn (int $id): bool =>
                    $id > 0
            )
            ->values();

        if (
            $allCartIds->count()
            !== $allCartIds
                ->unique()
                ->count()
        ) {
            throw ValidationException::withMessages([
                'cart_assignments' =>
                    'The same cart cannot be assigned more than once.',
            ]);
        }

        return DB::transaction(
            function () use (
                $booking,
                $normalizedAssignments,
                $allCartIds
            ): Booking {
                $lockedBooking = Booking::query()
                    ->whereKey(
                        $booking->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Lock the selected physical carts before performing
                 * availability reads. This serializes competing
                 * confirmation attempts for the same cart.
                 */
                $lockedCarts = Cart::query()
                    ->whereIn(
                        'id',
                        $allCartIds
                            ->sort()
                            ->values()
                            ->all()
                    )
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $items = $lockedBooking
                    ->items()
                    ->orderBy('id')
                    ->get();

                if ($items->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart_assignments' =>
                            'This booking has no cart items to assign.',
                    ]);
                }

                $bookingTypes = $items
                    ->pluck('cart_type')
                    ->all();

                foreach (
                    $normalizedAssignments as
                    $cartType => $cartIds
                ) {
                    if (
                        $cartIds !== []
                        && ! in_array(
                            $cartType,
                            $bookingTypes,
                            true
                        )
                    ) {
                        throw ValidationException::withMessages([
                            "cart_assignments.{$cartType}" =>
                                'This cart type is not part of the booking.',
                        ]);
                    }
                }

                foreach (
                    $items as $item
                ) {
                    $cartIds =
                        $normalizedAssignments[
                            $item->cart_type
                        ] ?? [];

                    if (
                        count($cartIds)
                        !== (int) $item->quantity
                    ) {
                        throw ValidationException::withMessages([
                            "cart_assignments.{$item->cart_type}" =>
                                "Select exactly {$item->quantity} cart(s) for this booking item.",
                        ]);
                    }

                    foreach (
                        $cartIds as $cartId
                    ) {
                        $cart =
                            $lockedCarts->get(
                                $cartId
                            );

                        if (! $cart) {
                            throw ValidationException::withMessages([
                                "cart_assignments.{$item->cart_type}" =>
                                    'One of the selected carts does not exist.',
                            ]);
                        }

                        if (
                            $cart->cart_type
                            !== $item->cart_type
                        ) {
                            throw ValidationException::withMessages([
                                "cart_assignments.{$item->cart_type}" =>
                                    "{$cart->code} is not the correct cart type.",
                            ]);
                        }

                        if (
                            $cart->operational_status
                            !== 'active'
                        ) {
                            throw ValidationException::withMessages([
                                "cart_assignments.{$item->cart_type}" =>
                                    "{$cart->code} is not operationally active.",
                            ]);
                        }

                        if (
                            ! $this->availability
                                ->isCartAvailable(
                                    $cart,
                                    $lockedBooking->pickup_at,
                                    $lockedBooking->return_at,
                                    $lockedBooking->id
                                )
                        ) {
                            throw ValidationException::withMessages([
                                "cart_assignments.{$item->cart_type}" =>
                                    "{$cart->code} is not available for the requested Belize date and time.",
                            ]);
                        }
                    }
                }

                foreach (
                    $items as $item
                ) {
                    $cartIds =
                        $normalizedAssignments[
                            $item->cart_type
                        ] ?? [];

                    $item
                        ->assignments()
                        ->delete();

                    foreach (
                        $cartIds
                        as $cartId
                    ) {
                        $item
                            ->assignments()
                            ->create([
                                'cart_id' =>
                                    $cartId,
                            ]);
                    }
                }

                $lockedBooking->update([
                    'status' =>
                        'confirmed',
                ]);

                return $lockedBooking
                    ->fresh([
                        'items.assignments.cart',
                    ]);
            }
        );
    }

    private function normalizeAssignments(
        array $cartIdsByType
    ): array {
        $normalized = [];

        foreach (
            $cartIdsByType as
            $cartType => $cartIds
        ) {
            if (! is_array($cartIds)) {
                $cartIds = [];
            }

            $normalized[
                (string) $cartType
            ] = collect($cartIds)
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->filter(
                    fn (int $id): bool =>
                        $id > 0
                )
                ->unique()
                ->values()
                ->all();
        }

        return $normalized;
    }
}