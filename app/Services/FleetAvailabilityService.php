<?php

namespace App\Services;

use App\Models\Cart;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class FleetAvailabilityService
{
    public function availableCarts(
        string $cartType,
        CarbonInterface|string $pickupAt,
        CarbonInterface|string $returnAt,
        ?int $excludeBookingId = null
    ): Collection {
        return $this
            ->availabilityQuery(
                $cartType,
                $pickupAt,
                $returnAt,
                $excludeBookingId
            )
            ->orderBy('code')
            ->get();
    }

    public function isCartAvailable(
        Cart $cart,
        CarbonInterface|string $pickupAt,
        CarbonInterface|string $returnAt,
        ?int $excludeBookingId = null
    ): bool {
        if ($cart->operational_status !== 'active') {
            return false;
        }

        return $this
            ->availabilityQuery(
                $cart->cart_type,
                $pickupAt,
                $returnAt,
                $excludeBookingId
            )
            ->whereKey($cart->id)
            ->exists();
    }

    public function availableCount(
        string $cartType,
        CarbonInterface|string $pickupAt,
        CarbonInterface|string $returnAt,
        ?int $excludeBookingId = null
    ): int {
        return $this
            ->availabilityQuery(
                $cartType,
                $pickupAt,
                $returnAt,
                $excludeBookingId
            )
            ->count();
    }

    protected function availabilityQuery(
        string $cartType,
        CarbonInterface|string $pickupAt,
        CarbonInterface|string $returnAt,
        ?int $excludeBookingId = null
    ): Builder {
        [
            $pickup,
            $return,
        ] = $this->normalizeWindow(
            $pickupAt,
            $returnAt
        );

        $bufferMinutes = max(
            0,
            (int) config(
                'grabone.booking_buffer_minutes',
                0
            )
        );

        /*
         * A confirmed booking occupies its cart from pickup_at
         * until return_at + the operational buffer.
         *
         * A requested booking also requires its own buffer after return_at.
         *
         * Therefore the two operational windows overlap when:
         *
         * existing pickup_at < requested return_at + buffer
         *
         * AND
         *
         * existing return_at > requested pickup_at - buffer
         *
         * Pending, completed, and cancelled bookings do not block.
         *
         * excludeBookingId is used when editing an existing confirmed
         * booking so that the booking does not conflict with itself.
         */

        $requestedStartBoundary = $pickup
            ->subMinutes($bufferMinutes)
            ->format('Y-m-d H:i:s');

        $requestedEndBoundary = $return
            ->addMinutes($bufferMinutes)
            ->format('Y-m-d H:i:s');

        return Cart::query()
            ->where(
                'cart_type',
                $cartType
            )
            ->where(
                'operational_status',
                'active'
            )
            ->whereNotIn(
                'id',
                function ($query) use (
                    $requestedStartBoundary,
                    $requestedEndBoundary,
                    $excludeBookingId
                ): void {
                    $query
                        ->select('assignments.cart_id')
                        ->from(
                            'booking_cart_assignments as assignments'
                        )
                        ->join(
                            'booking_items as items',
                            'items.id',
                            '=',
                            'assignments.booking_item_id'
                        )
                        ->join(
                            'bookings',
                            'bookings.id',
                            '=',
                            'items.booking_id'
                        )
                        ->where(
                            'bookings.status',
                            'confirmed'
                        )
                        ->when(
                            $excludeBookingId !== null,
                            fn ($query) =>
                                $query->where(
                                    'bookings.id',
                                    '!=',
                                    $excludeBookingId
                                )
                        )
                        ->where(
                            'bookings.pickup_at',
                            '<',
                            $requestedEndBoundary
                        )
                        ->where(
                            'bookings.return_at',
                            '>',
                            $requestedStartBoundary
                        );
                }
            );
    }

    protected function normalizeWindow(
        CarbonInterface|string $pickupAt,
        CarbonInterface|string $returnAt
    ): array {
        $pickup = $this->businessTime(
            $pickupAt
        );

        $return = $this->businessTime(
            $returnAt
        );

        if ($return->lessThanOrEqualTo($pickup)) {
            throw new InvalidArgumentException(
                'Return time must be after pickup time.'
            );
        }

        return [
            $pickup,
            $return,
        ];
    }

    protected function businessTime(
        CarbonInterface|string $value
    ): CarbonImmutable {
        $timezone = (string) config(
            'grabone.timezone',
            config(
                'app.timezone',
                'America/Belize'
            )
        );

        if ($value instanceof CarbonInterface) {
            return CarbonImmutable::instance(
                $value
            )->setTimezone($timezone);
        }

        return CarbonImmutable::parse(
            $value,
            $timezone
        );
    }
}