<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use DB;
class Policy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
       if ($request !== null) {
        // Access request properties
        $headers = $request->headers;
        // Your middleware logic
    } else {
        // Handle the case when the request object is null
        abort(500, 'Request object is null');
    }
    //   if (!in_array('admin', user_roles())) {
    //         $totalPolicies = DB::table('policies')->where('trash_status',0)->count();

    //         if($totalPolicies != 0){
    //             $userAcceptedPolicies = DB::table('pilicy_accept')
    //                 ->where('user_id', auth()->user()->id)
    //                 ->where('trash_status', 0)
    //                 ->count();
                
    //             if ($userAcceptedPolicies === $totalPolicies) {
                    return $next($request);
        //         } else {
        //             return redirect('police_accept?mess="Please Accept All Policies First"');
        //         }
        //     }
        // }

        
    }
}
