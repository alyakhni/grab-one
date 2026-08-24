<?php

namespace App\Support;

final class BookingCartSelection
{
    public static function cartTypes(): array
    {
        $types = [];

        foreach (config('fleet.carts', []) as $code => $cart) {
            $types[$code] = $cart['booking_label'] ?? $cart['name'] ?? $code;
        }

        return $types;
    }

    public static function selectionOptions(): array
    {
        $options = self::cartTypes();

        if (count($options) > 1) {
            $options['mix'] = 'Mix';
        }

        return $options;
    }

    public static function defaultSelection(): string
    {
        return array_key_first(self::cartTypes()) ?? '';
    }

    public static function defaultQuantities(): array
    {
        $default = (int) config('grabone.default_cart_quantity', 1);

        return array_fill_keys(
            array_keys(self::cartTypes()),
            max(1, $default)
        );
    }

    public static function quantityRules(): array
    {
        $rules = [];

        foreach (array_keys(self::cartTypes()) as $code) {
            $rules["cart_quantities.{$code}"] = [
                'nullable',
                'integer',
                'min:1',
            ];
        }

        return $rules;
    }

    public static function itemQuantities(
        string $selection,
        array $quantities
    ): array {
        $cartTypes = self::cartTypes();

        $selectedTypes = $selection === 'mix'
            ? array_keys($cartTypes)
            : [$selection];

        $default = max(
            1,
            (int) config('grabone.default_cart_quantity', 1)
        );

        $items = [];

        foreach ($selectedTypes as $cartType) {
            if (! array_key_exists($cartType, $cartTypes)) {
                continue;
            }

            $items[$cartType] = max(
                1,
                (int) ($quantities[$cartType] ?? $default)
            );
        }

        return $items;
    }
}