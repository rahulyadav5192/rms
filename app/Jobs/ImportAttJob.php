<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportAttJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $machineIp; // New property to store the machine IP

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @param string $machineIp
     */
    public function __construct(array $data, $machineIp)
    {
        $this->data = $data;
        $this->machineIp = $machineIp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            // Extract uid and id values from the data
            $uids = array_column($this->data, 'uid');
            $ids = array_column($this->data, 'id');

            // Check if the data already exists based on uid and id
            $existingData = DB::table('atte_dump')
                ->whereIn('uid', $uids)
                ->orWhereIn('id', $ids)
                ->get();

            // Filter out the data that already exists
            $filteredData = array_filter($this->data, function ($item) use ($existingData) {
                $isDuplicate = $existingData->contains(function ($existingItem) use ($item) {
                    return $existingItem->uid == $item['uid'] && $existingItem->id == $item['id'];
                });

                return !$isDuplicate;
            });

            // Add machine IP to each record
            foreach ($filteredData as &$item) {
                $item['machine_ip'] = $this->machineIp;
                $item['timestamp'] = $item['timestamp'];
            }

            // Insert the filtered data into the atte_dump table
            DB::table('atte_dump')->insert($filteredData);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->job->fail($e->getMessage());
        }
        
        info('ImportAttJob has completed.');
    }
}
