<?php
namespace App\Http\Traits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
 *  This trait will content all methods that dealing with api and paginate pages too.
 */
trait ApiResponse
{
  protected function successResponse($data, $code = 200)
  {
    return response()->json($data,$code);
  }
  protected function showOne(Model $model, $code=200){
    return response()->json(['data' => $model],$code);
  }
  protected function showAll(Collection $collection, $code=200, $prePage = 20, $additionalData = null){
    //$collection = $this->sortData($collection);
    if($prePage > 0)
        $collection = $this->paginate($collection, $prePage);
    if($additionalData == null)
        return response()->json(['data' => $collection ],$code);
    else
        return response()->json(['data' => $collection, $additionalData],$code);
  }
  protected function errorResponse($message, $code){
    return response()->json(['error' => $message],$code);
  }

  protected function paginate(Collection $collection,$prePage = 15){
        Validator::validate(request()->all(),[
            'pre_page' => 'integer|min:2|max:50'
        ]);
        if(request()->has('pre_page'))
            $prePage = (int)request()->pre_page;
        $page = LengthAwarePaginator::resolveCurrentPage(); // for get the crrent page
        //[LengthAwarePaginator] implament use Illuminate\Pagination\LengthAwarePaginator;
        //[prePage] => count of collection in page.
        // [(($page-1) * $prePage)] => this for calculate index of collection that will start from it.
        //[slice] => for cut collection from (start , end)
        $result = $collection->slice((($page-1) * $prePage), $prePage)->values();
        $paginate = new LengthAwarePaginator($result,$collection->count(),$prePage,$page,[
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);
        $paginate->appends(request()->all());
        return $paginate;
    }
    protected function sortData(Collection $collection){

        if(request()->has('sortby')){
            $collection = $collection->sortBy(request()->sort_by);
        }

        return $collection;
    }
    protected function filterData(Collection $collection , $filter){
        if(request()->has(''));
        return $collection;
    }
  public function validateDate($date, $format = 'Y-m-d H:i:s')
    {

        if($format == 'Y-m-d H:i:s'):
            $datetime = explode(' ',$date);
            if(count($datetime) == 2):
                $splitDate = explode('-',$datetime[0]);
                if(count($splitDate) == 3):
                    $year = intval($splitDate[0]);
                    $month = intval($splitDate[1]);
                    $day = intval($splitDate[2]);
                    if(!checkdate($month, $day, $year)) return false;
                    $splitDate = explode(':',$datetime[1]);
                    if(count($splitDate) == 3):
                        $hour = intval($splitDate[0]);
                        $minute = intval($splitDate[1]);
                        $second = intval($splitDate[2]);
                        if($hour >= 24 or $hour < 0) return false;
                        if($minute >= 60 or $minute < 0) return false;
                        if($second >= 60 or $second < 0) return false;
                        else return true;
                    else:
                        return false;
                    endif;
                endif;
            endif;

        elseif($format == 'Y-m-d'):
            $splitDate = explode('-',$date);
            if(count($splitDate) == 3):
                $year = intval($splitDate[0]);
                $month = intval($splitDate[1]);
                $day = intval($splitDate[2]);
                if(!checkdate($month, $day, $year)):
                    return false;
                endif;
            else:
                return false;
            endif;
        elseif ($format == 'H:i:s'):
            $splitDate = explode(':',$date);
            if(count($splitDate) == 3):
                $hour = intval($splitDate[0]);
                $minute = intval($splitDate[1]);
                $second = intval($splitDate[2]);
                if($hour >= 24 or $hour < 0) return false;
                if($minute >= 60 or $minute < 0) return false;
                if($second >= 60 or $second < 0) return false;
                else return true;
            else:
                return false;
            endif;
        else:
            return false;
        endif;

        return true;
    }
    public function compareDate($date1, $date2){
      //the date must be format like: 'Y-m-d'
        $d1 = new Carbon($date1);
        $d2 = new Carbon($date2);
        if($d1->greaterThan($d2) == 1)
            return 1; //this mean the date1 is greater than date2
        elseif($d1->lessThan($d2))
            return -1; //this mean the date1 is less than date2
        return 0; //this mean the date1 is equal date2
    }
}
