<?php

namespace Filament\Jetstream;

use Illuminate\Support\Collection;
use JsonSerializable;

class Role implements JsonSerializable
{
    /**
     * The key identifier for the role.
     *
     * @var string
     */
    public $key;

    /**
     * The name of the role.
     *
     * @var string
     */
    public $name;

    /**
     * The role's permissions.
     *
     * @var array
     */
    public $permissions;

    /**
     * The role's description.
     *
     * @var string
     */
    public $description;

    /** @var array{name:string, key:string, description:string, permissions:array<int, string>}|null */
    public static ?array $rolesAndPermissions = [
        [
            'key' => 'admin',
            'name' => 'System Administrator',
            'description' => 'Administrator users can perform any action.',
            'permissions' => [
                'create',
                'read',
                'update',
                'delete',
            ],
        ],
        [
            'key' => 'customer-editor',
            'name' => 'Customer Editor',
            'description' => 'Customer Editor users have the ability to read and update data.',
            'permissions' => [
                'read',
                'create',
                'update',
            ],
        ],
        [
            'key' => 'customer-viewer',
            'name' => 'Customer Viewer',
            'description' => 'Customer viewers can only read data.',
            'permissions' => [
                'read',
            ],
        ],
        [
            'key' => 'staff-admin',
            'name' => 'Staff Admin',
            'description' => 'Staff Admins can manage their own teams and users.',
            'permissions' => [
                'create',
                'read',
                'update',
                'delete',
            ],
        ],
        [
            'key' => 'staff-editor',
            'name' => 'Staff Editor',
            'description' => 'Staff Editors users have the ability to read and update data.',
            'permissions' => [
                'read',
                'create',
                'update',
            ],
        ],
        [
            'key' => 'staff-viewer',
            'name' => 'Staff Viewer',
            'description' => 'Staff viewers can only read data.',
            'permissions' => [
                'read',
            ],
        ],
    ];

    /**
     * Create a new role instance.
     *
     * @return void
     */
    public function __construct(string $key, string $name, array $permissions)
    {
        $this->key = $key;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    /**
     * Describe the role.
     *
     * @return $this
     */
    public function description(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Find the role with the given key.
     */
    public static function find(string $key): ?Role
    {
        return static::roles()->firstWhere('key', $key);
    }

    /**
     * Get all roles and their permissions.
     */
    public static function roles(): Collection
    {
        return collect(static::$rolesAndPermissions)
            ->map(fn (array $role) => (new static($role['key'], $role['name'], $role['permissions']))
                ->description($role['description']));
    }

    /**
     * Get the JSON serializable representation of the object.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'key' => $this->key,
            'name' => __($this->name),
            'description' => __($this->description),
            'permissions' => $this->permissions,
        ];
    }
}
