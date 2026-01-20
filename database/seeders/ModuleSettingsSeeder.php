<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleSetting;

class ModuleSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run($companyId)
    {

        $module = new ModuleSetting();
        $module->company_id = $companyId;
        $module->type = 'employee';
        $module->module_name = 'projects';
        $module->status = 'active';
        $module->save();

        $module = new ModuleSetting();
        $module->company_id = $companyId;
        $module->type = 'employee';
        $module->module_name = 'messages';
        $module->status = 'active';
        $module->save();

        $module = new ModuleSetting();
        $module->company_id = $companyId;
        $module->type = 'employee';
        $module->module_name = 'notices';
        $module->status = 'active';
        $module->save();

        $module = new ModuleSetting();
        $module->company_id = $companyId;
        $module->type = 'employee';
        $module->module_name = 'leads';
        $module->status = 'active';
        $module->save();


        // Client Module
        $module = new ModuleSetting();
        $module->company_id = $companyId;
        $module->type = 'client';
        $module->module_name = 'invoices';
        $module->status = 'active';
        $module->save();

        $module = new ModuleSetting();
        $module->company_id = $companyId;
        $module->type = 'client';
        $module->module_name = 'projects';
        $module->status = 'active';
        $module->save();

        $this->moduleSettings($companyId);

    }

    private function moduleSettings($companyId)
    {

        $clientModules = [
            'clients',
            'projects',
            'tickets',
            'invoices',
            'estimates',
            'events',
            'messages',
            'tasks',
            'timelogs',
            'contracts',
            'notices',
            'payments',
            'orders',
            'knowledgebase',
        ];

        $otherModules = [
            'employees',
            'attendance',
            'expenses',
            'leaves',
            'leads',
            'holidays',
            'products',
            'reports',
            'settings',
            'bankaccount'
        ];


        $data = [
            'admin' => [
                ...$clientModules, ...$otherModules
            ],
            'employee' => [
                ...$clientModules, ...$otherModules
            ]
            ,
            'client' => [
                ...$clientModules
            ]
        ];


        foreach ($data as $type => $moduleList) {

            foreach ($moduleList as $module) {

                $setting = new ModuleSetting();
                $setting->company_id = $companyId;
                $setting->type = $type;
                $setting->module_name = $module;
                $setting->status = 'active';
                $setting->save();
            }

        }

    }

}
