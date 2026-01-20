<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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
        // Perform your policy acceptance checks here
        // If user hasn't accepted all policies, abort the request with a 403 error
       if (!in_array('admin', user_roles())) {
            $totalPolicies = DB::table('policies')->where('trash_status',0)->count();

            if($totalPolicies != 0){
                $userAcceptedPolicies = DB::table('pilicy_accept')
                    ->where('user_id', auth()->user()->id)
                    ->where('trash_status', 0)
                    ->count();
                
                // Check if the user has accepted all policies
                if ($userAcceptedPolicies === $totalPolicies) {
                    return $next($request);
                } else {
                    return redirect('police_accept?mess="Please Accept All Policies First"');
                }
            }
        }

        
    }
}
