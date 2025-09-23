<?php

namespace Filament\Jetstream\Livewire\Teams;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Jetstream\Livewire\BaseLivewireComponent;
use Filament\Jetstream\Models\Team;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Log;

class UpdateTeamName extends BaseLivewireComponent
{
    public ?array $data = [];

    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team;

        $this->form->fill($team->only(['name']));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament-jetstream::default.update_team_name.section.title'))
                    ->aside()
                    ->description(__('filament-jetstream::default.update_team_name.section.description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-jetstream::default.form.team_name.label'))
                            ->string()
                            ->maxLength(255)
                            ->required()
                            ->unique(ignoreRecord: true),
                        Actions::make([
                            Action::make('save')
                                ->label(__('filament-jetstream::default.action.save.label'))
                                ->action(fn () => $this->updateTeamName($this->team)),
                        ])->alignEnd(),
                    ]),
            ])
            ->statePath('data');
    }

    public function updateTeamName(Team $team): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->sendRateLimitedNotification($exception);

            return;
        }

        $slug = str($this->data['name'])->slug();
        if (Team::where('slug', $slug)
            ->whereNot('id', $this->team->id)
            ->exists()) {
            Log::debug('Team slug already exists!', [
                $slug,
                'team_id' => $this->team->id,
            ]);

            $this->sendNotification('Cannot Save', 'Cannot use this team name as it is too similar to an existing team. Please try an alternative.', 'danger');
            return;
        }

        $data = $this->form->getState();

        $team->forceFill([
            'name' => $data['name'],
            'slug' => $slug,
        ])->save();

        $this->sendNotification();

        $this->redirect(route('filament.app.tenant.profile', $team), true);
    }

    public function render()
    {
        return view('filament-jetstream::livewire.teams.update-team-name');
    }
}
