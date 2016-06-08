<?php  namespace App\Libraries\Jfs;

use Colors\RandomColor;
use App\Models\Jfs\Site;
use App\Models\Jfs\Workflow;

class SiteDashboardService
{
    public function generateGraphData() {
        $reportData = [];
        $workFlows = Workflow::all();
        $totalSites = Site::all()->count();
        $colors = [
            'red', 'orange', 'yellow', 'green', 'blue', 'purple', 'pink',
        ];

        foreach($workFlows as $flow) {

            $taskNames = $flow->tasks->lists('name')->toArray();

            foreach($taskNames as $taskName) {

                $res = \DB::select('SELECT DISTINCT count(jfs_tasks.name) AS count FROM jfs_site_jfs_task INNER JOIN jfs_tasks ON jfs_site_jfs_task.jfs_task_id = jfs_tasks.id WHERE jfs_tasks.name = "' . $taskName . '" AND completed = 1 GROUP BY jfs_task_id');

                if(count($res)) {
                    $reportData[$flow->name][$taskName]['count'] = number_format($res[0]->count / $totalSites, 3) * 100;
                } else {
                    $reportData[$flow->name][$taskName]['count'] = 0;
                }

                $reportData[$flow->name][$taskName]['backgroundColor'] = RandomColor::one([ 'hue' => $colors[array_rand($colors, 1)]]);
                $reportData[$flow->name][$taskName]['hoverBackgroundColor'] = "#00C0EF";
            }
        }
        
        return $reportData;
    }
}