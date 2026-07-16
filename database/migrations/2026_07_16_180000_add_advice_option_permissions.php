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
                'name' => 'adviceoption',
                'sub_permission' => [
                    'adviceoption-list',
                    'adviceoption-add',
                    'adviceoption-edit',
                    'adviceoption-delete',
                ],
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
