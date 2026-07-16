<?php

use App\Helpers\SeederHelper;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'name' => 'advice',
                'sub_permission' => ['advice-list', 'advice-add', 'advice-edit', 'advice-delete'],
            ],
            [
                'name' => 'program',
                'sub_permission' => ['program-list', 'program-add', 'program-edit', 'program-delete'],
            ],
        ];

        SeederHelper::seedPermissions($permissions);

        $sub_permission = array_merge(
            array_column($permissions, 'name'),
            ...array_column($permissions, 'sub_permission')
        );

        $roles = [
            [
                'name' => 'admin',
                'permissions' => $sub_permission,
            ],
        ];

        SeederHelper::seedRoles($roles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
