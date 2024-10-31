<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\PaymentEnum;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Date;
use Pelmered\FilamentMoneyField\Forms\Components\MoneyInput;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return __('payment');
    }

    protected static ?string $title = 'Pembayaran';

    protected static ?string $breadcrumb = 'Pembayaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                TextInput::make('money')
                    ->label('Total bayar')
                    ->prefix('Rp')
                    ->numeric()
                    ->required(),
                Select::make('for')
                    ->label('Jenis Pembayaran')
                    ->multiple()
                    ->options(PaymentEnum::class)
                    ->required(),
                FileUpload::make('proof')
                    ->openable()
                    ->directory('payment')
                    ->image()
                    ->label(__('proof_of_payment'))
                    ->required(),
                Section::make('verifikasi')
                    ->hidden(function(){
                        if (auth()->user()->hasRole('student')) {
                            return true;
                        }
                    })
                    ->columns(2)
                    ->schema([
                        Checkbox::make('verified')
                            ->label('Verifikasi pembayaran ini'),
                        DatePicker::make('date_of_verifying')
                            ->default(now()),
                        
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.student.full_name')
                    ->label(__('full_name'))
                    ->searchable()
                    ->hidden(function(){
                        if (auth()->user()->hasRole('student')) {
                            return true;
                        }
                    }),
                TextColumn::make('money')
                    ->label('Pembayaran')
                    ->money("IDR")
                    ->sortable()
                    ->summarize(Sum::make()->money('IDR', 100)),
                TextColumn::make('for')
                    ->label('Jenis Pembayaran')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Tanggal Penyerahan Bukti')
                    ->date(),
                IconColumn::make('verified')
                    ->label('Verifikasi')
                    ->boolean()
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('date_of_verifying')
                    ->label('Tanggal verifikasi')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('student')) {
                    return $query->where('user_id', auth()->id());
                }
            })
            ->header(view('BankPayment'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
