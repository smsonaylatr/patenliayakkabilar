<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    
                    ->placeholder('-'),
                TextEntry::make('order_number'),
                TextEntry::make('status'),
                TextEntry::make('payment_status'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                TextEntry::make('ip_address')
                    ->label('IP Adresi')
                    ->placeholder('-'),
                TextEntry::make('cargo_company')
                    ->placeholder('-'),
                TextEntry::make('cargo_tracking_code')
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ,
                TextEntry::make('shipping_price')
                    ,
                TextEntry::make('discount_total')
                    ,
                TextEntry::make('grand_total')
                    ,
                TextEntry::make('customer_name')
                    ->placeholder('-'),
                TextEntry::make('customer_phone')
                    ->placeholder('-'),
                TextEntry::make('customer_email')
                    ->placeholder('-'),
                TextEntry::make('customer_note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('shipping_city')
                    ->placeholder('-'),
                TextEntry::make('shipping_district')
                    ->placeholder('-'),
                TextEntry::make('shipping_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('billing_city')
                    ->placeholder('-'),
                TextEntry::make('billing_district')
                    ->placeholder('-'),
                TextEntry::make('billing_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
