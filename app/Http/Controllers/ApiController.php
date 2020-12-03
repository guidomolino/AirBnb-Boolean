<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// richiama Ui Message Request
use App\Http\Requests\UiMessageRequest;

use App\Flat;
use App\Message;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{

    public function flatSearch(Request $request) {

      $data = $request -> all();
      // dd($data);
      $loc = $data['loc'];
      $lat = $data['lat'];
      $lon = $data['lon'];

      if (is_numeric($data['distance'])) {
        $dist = $data['distance'];
      } else {
        $dist = 20;
      }

      if (is_numeric($data['rooms'])) {
        $rooms = $data['rooms'];
      } else {
        $rooms = 0;
      }

      if (is_numeric($data['beds'])) {
        $beds = $data['beds'];
      } else {
        $beds = 0;
      }

      if (isset($data['services'])) {
        $services = $data['services'];
      }
      else {
        for ($i=1; $i < 7; $i++) {
          $services[] = $i;
        }
      }
      // dd($services);

      // prendo gli appartamenti in base ai filtri impostati
      $distance = "( 6367 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) )  AS distance";

      $flats = Flat::select("flats.*",
                        DB::raw($distance),
                        DB::raw("(select photos.url from photos where photos.flat_id=flats.id LIMIT 1) AS url"),
                        DB::raw("(select flat_sponsor.sponsor_id from flat_sponsor where (flat_sponsor.flat_id=flats.id AND flat_sponsor.expires_at >= NOW()) LIMIT 1) AS sponsored")
                        )
        -> join('flat_service', 'flat_service.flat_id', '=', 'flats.id')
        -> join('services', 'services.id', '=', 'flat_service.service_id')
        -> join('photos', 'photos.flat_id', '=', 'flats.id')
        -> leftJoin('flat_sponsor', 'flat_sponsor.flat_id', '=', 'flats.id')
        -> where([
            ['flats.rooms','>=',$rooms],
            ['flats.beds', '>=', $beds]
          ])
        -> whereIn('flat_service.service_id',$services)
        -> having('distance', '<', $dist)
        -> orderBy('sponsored','DESC')
        -> orderBy('distance','ASC')
        -> groupBy('flats.id')
        -> get();

      return response() -> json($flats);
    }

    public function messageStore(UiMessageRequest $request) {

      // valido i dati attraberso l'UiMessageRequest e salvo i messaggi nel DB
      $validatedData = $request -> all();
      $mex = Message::create($validatedData);

      if ($mex) {
        // ritorno messaggio tutto ok da server
        return response() -> json(200);
        // response()->json(['success'=>"Il suo messaggio è stato ricevuto, la contatteremo a breve."]);
        // return back() -> with("status", "Il suo messaggio è stato ricevuto, la contatteremo a breve.");
      }
    }

    public function flatSponsorMake(Request $request, $id) {

      $data = $request -> all();
      // dd($data);
      $flat = Flat::findOrFail($id);

      $sponsor = $data['sponsor'];
      switch ($sponsor) {
      case 1:
          $amount = 2.99;
          $expires = Carbon::now() -> addHours(24);
          break;
      case 2:
          $amount = 5.99;
          $expires = Carbon::now() -> addHours(72);
          break;
      case 3:
          $amount = 9.99;
          $expires = Carbon::now() -> addHours(144);
          break;
        }

      $gateway = new \Braintree\Gateway([
        'environment' => env('BRAINTREE_ENVIRONMENT'),
        'merchantId' => env("BRAINTREE_MERCHANT_ID"),
        'publicKey' => env("BRAINTREE_PUBLIC_KEY"),
        'privateKey' => env("BRAINTREE_PRIVATE_KEY")
      ]);

      if($data['nonce'] !== null){

        $nonceFromTheClient = $data['nonce'];
        $gateway -> transaction() ->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonceFromTheClient,
            'options' => [
            'submitForSettlement' => True
            ]
        ]);

        $flat -> sponsors() -> attach($sponsor, ['expires_at' => $expires]);

        return response() -> json(200);
      }
      else {

        $token = $gateway -> clientToken() -> generate();
        return response() -> json(400);
      }
    }

}
