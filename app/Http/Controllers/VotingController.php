<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\pull_main_key;
use App\pull_sub_keys;
use App\classes\PullEngine;

class VotingController extends Controller
{
  private $pullEngine;

  public function __construct(){
    $this->pullEngine = new PullEngine();
  }

    public function ParseVoteQuery(Request $request){
        $query= explode(" ",$request->text);
        $allMainKeys = pull_main_key::pluck('name','id')->all();
        if (false !== $mainKey_id = array_search($query[0], $allMainKeys)) {
          $mainKey = pull_main_key::find($mainKey_id);

            // main key is present but may be disabled by user/reseller
            if($mainKey->user_enable_status && $mainKey->reseller_enable_status){

                $allSubKeys =pull_sub_keys::where('main_key_id',$mainKey_id)->pluck('name','id')->all();
                if (false !== $subKey_id = array_search($query[1], $allSubKeys)) {
                  $subKey = pull_sub_keys::find($subKey_id);
                  $subKey->count+=1;
                  $subKey->save();
                  return response([
                      'message' => $subKey->sucess_message,
                      'count' =>$subKey->count
                  ], 200);
                }else{
                  //subkey not found i.e probably syntax error
                  return response([
                      'message' =>$mainKey->failure_message,
                  ], 400);

              }

        }else{
          //the main key is disabled
          return response([
              'message' => $mainKey->disable_message,
          ], 403);//forbidden
        }
        }else{
          //either mainkey typo or wrong shortcode hit by user
          return response([
              'message' => "Oops! main key not found"
          ], 400);

        }


    }

    public function ParseResultQuery(Request $request){

      $query= explode(" ",$request->text);
      // print_r($query);
      // return;
      if(sizeof($query)!=2){
        return response([
            'result' => "sorry, your syntax is wrong"
        ], 400);
      }
      $allMainKeys = pull_main_key::pluck('name','id')->all();
      if (false !== $mainKey_id = array_search($query[0], $allMainKeys)) {
        $mainKey = pull_main_key::find($mainKey_id);
        if($mainKey->category_id==5){
          $this->pullEngine->LoadFile($mainKey->file_id);//code are shared from the PullEngine

          if( ($result = $this->pullEngine->GetResult($query[1]) )!=null ){//code are shared from the PullEngine
            return response([
                'result' => $result,
                'message'=>'success'
            ], 200);
          }else{
            return response([
                'result' => $mainKey->failure_message,
                'return' => $result,
                'message'=>'no result found'
            ], 403);
          }
          // $mainKey->file_id
          return ;
        }else{
          return response([
              'message' => "you entered worng main key"
          ], 400);
        }

      }
        // echo"result will be published soon";


    }
    public function TestVoteQuery(Request $request){
        $query= explode(" ",$request->text);
        $allMainKeys = pull_main_key::pluck('name','id')->all();
        if (false !== $mainKey_id = array_search($query[0], $allMainKeys)) {
          $mainKey = pull_main_key::find($mainKey_id);

            // main key is present but may be disabled by user/reseller
            if($mainKey->user_enable_status && $mainKey->reseller_enable_status){

                $allSubKeys =pull_sub_keys::where('main_key_id',$mainKey_id)->pluck('name','id')->all();
                if (false !== $subKey_id = array_search($query[1], $allSubKeys)) {
                  $subKey = pull_sub_keys::find($subKey_id);
                  $subKey->count+=1;
                  $subKey->save();
                  return response([
                      'message' => $subKey->sucess_message,
                      'count' =>$subKey->count
                  ], 200);
                }else{
                  //subkey not found i.e probably syntax error
                  return response([
                      'message' =>$mainKey->failure_message,
                  ], 400);

              }

        }else{
          //the main key is disabled
          return response([
              'message' => $mainKey->disable_message,
          ], 403);//forbidden
        }
        }else{
          //either mainkey typo or wrong shortcode hit by user
          return response([
              'message' => "Oops! main key not found"
          ], 400);

        }


    }

    public function TestResultQuery(Request $request){

      $query= explode(" ",$request->text);
      // print_r($query);
      // return;
      if(sizeof($query)!=2){
        return response([
            'result' => "sorry, your syntax is wrong"
        ], 400);
      }
      $allMainKeys = pull_main_key::pluck('name','id')->all();
      if (false !== $mainKey_id = array_search($query[0], $allMainKeys)) {
        $mainKey = pull_main_key::find($mainKey_id);
        if($mainKey->category_id==5){
          $this->pullEngine->LoadFile($mainKey->file_id);//code are shared from the PullEngine

          if( ($result = $this->pullEngine->GetResult($query[1]) )!=null ){//code are shared from the PullEngine
            return response([
                'result' => $result,
                'message'=>'success'
            ], 200);
          }else{
            return response([
                'result' => $mainKey->failure_message,
                'return' => $result,
                'message'=>'no result found'
            ], 400);
          }
          // $mainKey->file_id
          return ;
        }else{
          return response([
              'message' => "you entered worng main key"
          ], 400);
        }

      }
        // echo"result will be published soon";


    }
}
