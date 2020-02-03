<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shoe;

class ShoeController extends ApiController
{
    public function index(Request $request)
    {
        $search    = '';
        $orderBy   = $request->orderBy == null ? 'id' : $request->orderBy;
        $type      = $request->type == 'true' ? 'DESC' : 'ASC';
        $perPage   = $request->perPage == null ? 10 : $request->perPage;
        
        if (!empty($request->search)) {
            $search = $request->search;
        }

        $shoes = Shoe::search($search)->orderBy($orderBy, $type)->where('status', true)->paginate($perPage);

        return response()->json(
            [
                'status' => 'ok',
                'data'   => $shoes,
            ],
            200
        )
        ->header('Content-Type', 'application/json')
            ->header('charset', 'utf-8');

        return $this->showAll($shoes);
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required'      => 'The Name is required.',
            'color.required'     => 'The Color is required.',
            'size.required'      => 'The Size is required.',
            'price.required'     => 'The Price is required.',
            'size.numer'         => 'the data must be numbered.',
        ];

        $validator = \Validator::make(
            $request->all(),
            [
                'name'     => 'required',
                'color'    => 'required',
                'size'     => 'required|numeric',
                'price'     => 'required'
            ],
            $messages
        );

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->all(),422);
        }
        
        $shoe              = new Shoe();
        $shoe->name        = $request->input('name');
        $shoe->color       = $request->input('color');
        $shoe->size        = $request->input('size');
        $shoe->price       = $request->input('price');
        $shoe->description = $request->input('description');
        $shoe->status = 1;
        $shoe->save();

        return $this->showOne($shoe, 201);
    }


    public function show(Shoe $shoe)
    {
        return $this->showOne($shoe);
    }

    public function update(Request $request, Shoe $shoe)
    {
        $messages = [
            'name.required'      => 'The Name is required.',
            'color.required'     => 'The Color is required.',
            'size.required'      => 'The Size is required.',
            'price.required'     => 'The Price is required.',
            'size.numer'         => 'the data must be numbered.'
        ];

        $validator = \Validator::make(
            $request->all(),
            [
                'name'     => 'required',
                'color'    => 'required',
                'size'     => 'required|numeric',
                'price'     => 'required'
            ],
            $messages
        );

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->all(),422);
        }

        $shoe->name   = $request->name;
        $shoe->color  = $request->color;
        $shoe->size   = $request->size;
        $shoe->price  = $request->price;
        $shoe->status = $request->status;

        if (!$shoe->isDirty()) {
            return $this->errorResponse('you must specify at least one different value to update.',422);
        }
        
        $shoe->save();

        return $this->showOne($shoe, 201);
    }

    public function delete(Shoe $shoe)
    {
        $shoe->delete();

        return $this->showOne($shoe);
    }
        
}
