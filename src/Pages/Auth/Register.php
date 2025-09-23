<?php

namespace Filament\Jetstream\Pages\Auth;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Register extends \Filament\Auth\Pages\Register
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        if (Filament::hasTenancy()) {
            Log::debug('Creating team for user ' . $user->id);
            $team = $user->ownedTeams()->create([
                'name' => __('filament-jetstream::default.form.team_name.default_name'),
                'slug' => str(__('filament-jetstream::default.form.team_name.default_name') . '-' . $user->id)->slug(),
                'personal_team' => true,
            ]);

            Log::debug('Created Team', [$team]);

            $user->switchTeam($team);
        }

        return $user;
    }
}
