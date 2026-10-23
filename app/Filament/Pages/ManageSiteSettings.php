<?php

namespace App\Filament\Pages;

use App\Filament\NavigationGroup;
use App\Filament\Support\Icons;
use App\Filament\Support\Translations;
use App\Support\SiteSettings;
use App\Support\Translatable;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Edits the key/value rows behind App\Support\SiteSettings.
 */
class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'site-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('admin.settings.title');
    }

    public function getTitle(): string
    {
        return __('admin.settings.title');
    }

    public function mount(SiteSettings $settings): void
    {
        $this->form->fill($settings->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make(__('admin.settings.tabs.general'))
                            ->icon(Heroicon::OutlinedBuildingOffice2)
                            ->schema($this->generalTab()),
                        Tab::make(__('admin.settings.tabs.home'))
                            ->icon(Heroicon::OutlinedHome)
                            ->schema($this->homeTab()),
                        Tab::make(__('admin.settings.tabs.about'))
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema($this->aboutTab()),
                        Tab::make(__('admin.settings.tabs.trust'))
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->schema($this->trustTab()),
                        Tab::make(__('admin.settings.tabs.seo'))
                            ->icon(Heroicon::OutlinedMagnifyingGlass)
                            ->schema([
                                Translations::tabs(fn (string $locale) => [
                                    Textarea::make("meta_description.{$locale}")
                                        ->label(__('admin.settings.fields.meta_description'))
                                        ->rows(3)
                                        ->maxLength(300),
                                ]),
                                Section::make(__('admin.settings.fields.google'))
                                    ->schema([
                                        TextInput::make('ga_measurement_id')
                                            ->label(__('admin.settings.fields.ga_measurement_id'))
                                            ->helperText(__('admin.settings.fields.ga_measurement_id_help'))
                                            ->regex('/^G-[A-Z0-9]+$/')
                                            ->placeholder('G-XXXXXXXXXX'),
                                        TextInput::make('google_site_verification')
                                            ->label(__('admin.settings.fields.google_site_verification'))
                                            ->helperText(__('admin.settings.fields.google_site_verification_help'))
                                            ->maxLength(100),
                                        TextInput::make('google_business_url')
                                            ->label(__('admin.settings.fields.google_business_url'))
                                            ->helperText(__('admin.settings.fields.google_business_url_help'))
                                            ->url(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('admin.settings.save'))
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(SiteSettings $settings): void
    {
        $settings->set($this->form->getState());

        Notification::make()
            ->success()
            ->title(__('admin.settings.saved'))
            ->send();
    }

    /**
     * @return array<Component>
     */
    protected function generalTab(): array
    {
        return [
            Section::make()
                ->schema([
                    Translations::tabs(fn (string $locale) => [
                        TextInput::make("site_name.{$locale}")
                            ->label(__('admin.settings.fields.site_name'))
                            ->required()
                            ->maxLength(255),
                        Textarea::make("address.{$locale}")
                            ->label(__('admin.settings.fields.address'))
                            ->rows(2),
                    ]),
                    FileUpload::make('site_logo')
                        ->label(__('admin.settings.fields.site_logo'))
                        ->image()
                        ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp', 'image/jpeg'])
                        ->disk('public')
                        ->directory('branding'),
                ]),
            Section::make(__('admin.sections.contact'))
                ->schema([
                    Grid::make(['md' => 2])
                        ->schema([
                            TextInput::make('contact_email')
                                ->label(__('admin.settings.fields.contact_email'))
                                ->email()
                                ->required(),
                            TextInput::make('notification_email')
                                ->label(__('admin.settings.fields.notification_email'))
                                ->helperText(__('admin.settings.fields.notification_email_help'))
                                ->email(),
                            TextInput::make('contact_phone')
                                ->label(__('admin.settings.fields.contact_phone'))
                                ->helperText(__('admin.settings.fields.contact_phone_help'))
                                ->tel(),
                            TextInput::make('whatsapp')
                                ->label(__('admin.settings.fields.whatsapp'))
                                ->helperText(__('admin.settings.fields.whatsapp_help'))
                                ->tel(),
                            TextInput::make('linkedin_url')
                                ->label(__('admin.settings.fields.linkedin_url'))
                                ->url(),
                            TextInput::make('x_url')
                                ->label(__('admin.settings.fields.x_url'))
                                ->url(),
                            TextInput::make('map_url')
                                ->label(__('admin.settings.fields.map_url'))
                                ->url(),
                            TextInput::make('request_service_url')
                                ->label(__('admin.settings.fields.request_service_url'))
                                ->helperText(__('admin.settings.fields.request_service_url_help'))
                                ->url(),
                        ]),
                ]),
            Section::make(__('admin.settings.fields.national_address'))
                ->description(__('admin.settings.fields.national_address_help'))
                ->schema([
                    Grid::make(['md' => 3])
                        ->schema([
                            TextInput::make('national_address.building_number')
                                ->label(__('admin.settings.fields.building_number'))
                                ->maxLength(10),
                            TextInput::make('national_address.additional_number')
                                ->label(__('admin.settings.fields.additional_number'))
                                ->maxLength(10),
                            TextInput::make('national_address.postal_code')
                                ->label(__('admin.settings.fields.postal_code'))
                                ->maxLength(10),
                            TextInput::make('national_address.short_address')
                                ->label(__('admin.settings.fields.short_address'))
                                ->maxLength(12),
                        ]),
                    Translations::tabs(fn (string $locale) => [
                        Grid::make(['md' => 3])
                            ->schema([
                                TextInput::make("national_address.street.{$locale}")
                                    ->label(__('admin.settings.fields.street')),
                                TextInput::make("national_address.district.{$locale}")
                                    ->label(__('admin.settings.fields.district')),
                                TextInput::make("national_address.city.{$locale}")
                                    ->label(__('admin.settings.fields.city')),
                            ]),
                    ]),
                    TextInput::make('map_location')
                        ->label(__('admin.settings.fields.map_location'))
                        ->helperText(__('admin.settings.fields.map_location_help'))
                        ->maxLength(255),
                ]),
        ];
    }

    /**
     * @return array<Component>
     */
    protected function homeTab(): array
    {
        return [
            Translations::tabs(fn (string $locale) => [
                TextInput::make("hero_title.{$locale}")
                    ->label(__('admin.settings.fields.hero_title'))
                    ->required()
                    ->maxLength(255),
                Textarea::make("hero_subtitle.{$locale}")
                    ->label(__('admin.settings.fields.hero_subtitle'))
                    ->rows(3),
                Textarea::make("home_intro.{$locale}")
                    ->label(__('admin.settings.fields.home_intro'))
                    ->rows(4),
            ]),
            static::cinematicImage('hero_image'),
            Repeater::make('home_stats')
                ->label(__('admin.settings.fields.home_stats'))
                ->schema([
                    TextInput::make('value')
                        ->label(__('admin.settings.fields.stat_value'))
                        ->required()
                        ->maxLength(20),
                    Select::make('icon')
                        ->label(__('admin.settings.fields.stat_icon'))
                        ->options(Icons::options(['compass', 'chart', 'building', 'star', 'briefcase', 'rocket', 'network', 'shield'])),
                    ...collect(config('site.locales'))
                        ->map(fn (array $locale, string $key) => TextInput::make("label.{$key}")
                            ->label(__('admin.settings.fields.stat_label').' — '.$locale['name'])
                            ->extraInputAttributes(['dir' => $locale['dir']])
                            ->required())
                        ->values()
                        ->all(),
                ])
                ->columns(['md' => 2, 'xl' => 4])
                ->defaultItems(0)
                ->addActionLabel(__('admin.add_item'))
                ->reorderable()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<Component>
     */
    protected function aboutTab(): array
    {
        return [
            Translations::tabs(fn (string $locale) => [
                Textarea::make("about_story.{$locale}")
                    ->label(__('admin.settings.fields.about_story'))
                    ->rows(5),
                Textarea::make("about_team.{$locale}")
                    ->label(__('admin.settings.fields.about_team'))
                    ->rows(4),
                Textarea::make("mission.{$locale}")
                    ->label(__('admin.settings.fields.mission'))
                    ->rows(3),
                Textarea::make("vision.{$locale}")
                    ->label(__('admin.settings.fields.vision'))
                    ->rows(3),
            ]),
            static::cinematicImage('about_image'),
        ];
    }

    /**
     * Licences and accreditations (commercial registration, chamber membership, ISO...),
     * listed in the footer and on the About page. Hidden items stay saved but off the site.
     *
     * @return array<Component>
     */
    protected function trustTab(): array
    {
        return [
            Repeater::make('credentials')
                ->label(__('admin.settings.fields.credentials'))
                ->helperText(__('admin.settings.fields.credentials_help'))
                ->schema([
                    ...collect(config('site.locales'))
                        ->map(fn (array $locale, string $key) => TextInput::make("name.{$key}")
                            ->label(__('admin.settings.fields.credential_name').' — '.$locale['name'])
                            ->extraInputAttributes(['dir' => $locale['dir']])
                            ->required()
                            ->maxLength(120))
                        ->values()
                        ->all(),
                    TextInput::make('number')
                        ->label(__('admin.settings.fields.credential_number'))
                        ->maxLength(60),
                    TextInput::make('url')
                        ->label(__('admin.settings.fields.credential_url'))
                        ->url(),
                    FileUpload::make('logo')
                        ->label(__('admin.settings.fields.credential_logo'))
                        ->image()
                        ->disk('public')
                        ->directory('credentials'),
                    Toggle::make('visible')
                        ->label(__('admin.settings.fields.credential_visible'))
                        ->default(true),
                ])
                ->columns(['md' => 2])
                ->defaultItems(0)
                ->addActionLabel(__('admin.add_item'))
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => Translatable::pick($state['name'] ?? null))
                ->columnSpanFull(),
        ];
    }

    /**
     * An optional full-bleed background photo, stored on the public disk under branding/.
     */
    protected static function cinematicImage(string $key): FileUpload
    {
        return FileUpload::make($key)
            ->label(__("admin.settings.fields.{$key}"))
            ->helperText(__("admin.settings.fields.{$key}_help"))
            ->image()
            ->imageEditor()
            ->disk('public')
            ->directory('branding');
    }
}
