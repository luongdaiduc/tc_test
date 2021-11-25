<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parcel;
use App\Repository\Eloquent\ParcelRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ParcelController extends Controller
{
    private $parcelRepository;

    public function __construct(ParcelRepository $parcelRepository)
    {
        $this->parcelRepository = $parcelRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $data = $this->parcelRepository->getDetail($id);

        if (is_null($data)) {
            return $this->sendError('Parcel not found.');
        }

        return $this->sendResponse($data);
    }

    /**
     * create new parcel
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        // validate inputs
        $validator = Validator::make($request->all(), [
            'item_name'         => 'required|string|max:255',
            'weight'            => 'required',
            'volume'            => 'required',
            'declared_value'    => 'required',
        ]);

        // validation failed
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $validData = $validator->valid();

        $weight         = $validData['weight'];
        $volume         = $validData['volume'];
        $declaredValue  = $validData['declared_value'];

        $quote = $this->getQuote($weight, $volume, $declaredValue);

        try {
            // create new parcel
            $user = $this->parcelRepository->create([
                'item_name'         => $validData['item_name'],
                'weight'            => $weight,
                'volume'            => $volume,
                'declared_value'    => $declaredValue,
                'chosen_model'      => $quote[0],
                'quote'             => $quote[1],
            ]);

            return $this->sendResponse($user, 'Create new parcel successfully');
        } catch (QueryException $e) {
            Log::error('Create new parcel failed with error: ' . $e->getMessage());

            return $this->sendError('Try to create parcel failed');
        }
    }

    /**
     * get quote for parcel
     * @param $weight
     * @param $volume
     * @param $declaredValue
     * @return array
     */
    public function getQuote($weight, $volume, $declaredValue) {
        $weightQuote        = $weight * config('app.weight_rate');
        $volumeQuote        = $volume * config('app.volume_rate');
        $declaredValueQuote = $declaredValue * config('app.value_rate');

        $quotes = [
            'weight'        => $weightQuote,
            'volume'        => $volumeQuote,
            'declaredValue' => $declaredValueQuote,
        ];

        // sort ascending by quote's value
        asort($quotes);

        $chosenModel    = '';
        $quote          = 0;

        // get chosen model and quote
        // because we sort ascending so when the loop end, we'll have the right value
        foreach ($quotes as $index => $value) {
            $chosenModel    = $index;
            $quote          = $value;
        }

        return [$chosenModel, $quote];
    }

    /**
     * Update parcel.
     *
     * @param Request $request
     * @param Parcel $parcel
     * @return JsonResponse
     */
    public function update(Request $request, Parcel $parcel)
    {
        $validator = Validator::make($request->all(), [
            'item_name'         => 'string|max:255',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());
        }

        $validData = $validator->valid();

        // if newest data isn't input, use the old one
        $itemName       = $validData['item_name'] ?? $parcel->item_name;
        $weight         = $validData['weight'] ?? $parcel->weight;
        $volume         = $validData['volume'] ?? $parcel->volume;
        $declaredValue  = $validData['declared_value'] ?? $parcel->declared_value;

        $quote = $this->getQuote($weight, $volume, $declaredValue);

        try {
            $updateData = [
                'item_name'         => $itemName,
                'weight'            => $weight,
                'volume'            => $volume,
                'declared_value'    => $declaredValue,
                'chosen_model'      => $quote[0],
                'quote'             => $quote[1],
            ];

            // update user
            $this->parcelRepository->update(['id' => $parcel->id], $updateData);

            return $this->sendResponse($updateData, 'Update parcel successfully');
        } catch (QueryException $e) {
            Log::error('Update parcel failed with error: ' . $e->getMessage());

            return $this->sendError('Try to update parcel failed');
        }
    }

    /**
     * delete parcel
     *
     * @param Request $request
     * @param Parcel $parcel
     * @return JsonResponse
     */
    public function delete(Request $request, Parcel $parcel)
    {
        try {
            // delete parcel
            $this->parcelRepository->delete(['id' => $parcel->id]);

            return $this->sendResponse('', 'Delete parcel successfully');
        } catch (QueryException $e) {
            Log::error('Delete parcel failed with error: ' . $e->getMessage());

            return $this->sendError('Try to delete parcel failed');
        }
    }

}
