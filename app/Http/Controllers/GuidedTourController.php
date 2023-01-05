<?php

namespace App\Http\Controllers;


class GuidedTourController extends AppBaseController {
    
    
    public function __construct() {
        $this->middleware('auth'); 
    }
    
    public function init() {
        if(!\Session::get("tour")){
            //set the tour session
            echo "<pre>";
            print_r(\Auth::user()->id);
            echo "</pre>";
        }
    }
    
    
    public function test(){
        
        $this->init();
        
        echo "<pre>";
        print_r(\Session::get("tour"));
        echo "</pre>";
    }
}
