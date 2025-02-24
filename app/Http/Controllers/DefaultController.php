<?php

namespace App\Http\Controllers;

use App\Models\LunarMission;
use App\Models\SpaceFlight;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DefaultController extends Controller
{
    private const FORBIDDEN = '{
"message": "Forbidden for you"
}';
    private const NOT_FOUND = '
    {
"message": "Not found",
"code": 404
}
    ';
    public function getGagarinFlight(Request $request)
    {
        if (!$this->checkAuth($request)) {
            return response()->json(json_decode(self::FORBIDDEN));
        }
        $responseValue = '{
    "data": [
        {
            "mission": {
                "name": "Восток 1",
                "launch_details": {
                    "launch_date": "1961-04-12",
                    "launch_site": {
                        "name": "Космодром Байконур",
                        "location": {
                            "latitude": "45.9650000",
                            "longitude": "63.3050000"
                        }
                    }
                },
                "flight_duration": {
                    "hours": 1,
                    "minutes": 48
                },
                "spacecraft": {
                    "name": "Восток 3KA",
                    "manufacturer": "OKB-1",
                    "crew_capacity": 1
                }
            },
            "landing": {
                "date": "1961-04-12",
                "site": {
                    "name": "Смеловка",
                    "country": "СССР",
                    "coordinates": {
                        "latitude": "51.2700000",
                        "longitude": "45.9970000"
                    }
                },
                "details": {
                    "parachute_landing": true,
                    "impact_velocity_mps": 7
                }
            },
            "cosmonaut": {
                "name": "Юрий Гагарин",
                "birthdate": "1934-03-09",
                "rank": "Старший лейтенант",
                "bio": {
                    "early_life": "Родился в Клушино, Россия.",
                    "career": "Отобран в отряд космонавтов в 1960 году...",
                    "post_flight": "Стал международным героем."
                }
            }
        }
    ]
}';

        return response()->json(json_decode($responseValue));
    }

    public function getFlight()
    {
        $responseValue = '{
    "data": {
        "name": "Аполлон-11",
        "crew_capacity": 3,
        "cosmonaut": [
            {
                "name": "Нил Армстронг",
                "role": "Командир"
            },
            {
                "name": "Базз Олдрин",
                "role": "Пилот лунного модуля"
            },
            {
                "name": "Майкл Коллинз",
                "role": "Пилот командного модуля"
            }
        ],
        "launch_details": {
            "launch_date": "1969-07-16",
            "launch_site": {
                "name": "Космический центр имени Кеннеди",
                "latitude": "28.5721000",
                "longitude": "-80.6480000"
            }
        },
        "landing_details": {
            "landing_date": "1969-07-20",
            "landing_site": {
                "name": "Море спокойствия",
                "latitude": "0.6740000",
                "longitude": "23.4720000"
            }
        }
    }
}';

        return response()->json(json_decode($responseValue));
    }

    public function addLunarMission(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'mission' => 'required',
            'mission.name' => 'required|regex:/^[A-ZА-ЯЁ].*$/u',
            'mission.launch_details' => 'required',
            'mission.launch_details.launch_date' => ['required', Rule::date()->format('Y-m-d')],
            'mission.launch_details.launch_site' => 'required',
            'mission.launch_details.launch_site.name' => 'required',
            'mission.launch_details.launch_site.location' => 'required',
            'mission.launch_details.launch_site.location.latitude' => 'required|decimal:1,10',
            'mission.launch_details.launch_site.location.longitude' => 'required|decimal:1,10',
            'mission.landing_details' => 'required',
            'mission.landing_details.landing_date' => ['required', Rule::date()->format('Y-m-d')],
            'mission.landing_details.landing_site' => 'required',
            'mission.landing_details.landing_site.name' => 'required',
            'mission.landing_details.landing_site.coordinates' => 'required',
            'mission.landing_details.landing_site.coordinates.latitude' => 'required|decimal:1,10',
            'mission.landing_details.landing_site.coordinates.longitude' => 'required|decimal:1,10',
            'mission.spacecraft' => 'required',
            'mission.spacecraft.command_module' => 'required',
            'mission.spacecraft.lunar_module' => 'required',
            'mission.spacecraft.crew' => 'required|array',
            'mission.spacecraft.crew.*.name' => 'required',
            'mission.spacecraft.crew.*.role' => 'required',
        ],
            [
                'name.regex' => 'Поле :attribute должно начинаться с большой буквы.',
            ]
        );

        if ($validated->errors()->isNotEmpty()) {
            $error = '{
    "error": {
        "code": 422,
        "message": "Not valid"
    }
}';
            $error = json_decode($error);
            $error->error->errors = $validated->errors()->getMessages();

            return response()->json($error);
        }
        $mission = new LunarMission();
        $mission->missions = $request->all();
        $mission->save();
        $response = '{
"data": {
"code": 201,
"message": "Миссия добавлена"
}
}';

        return response()->json(json_decode($response), 201);
    }

    public function getLunarMissions()
    {
        $missions = LunarMission::all('missions')->map(fn(LunarMission $mission) => $mission->missions);

        return response()->json($missions->all());
    }

    public function deleteLunarMission(Request $request, int $id)
    {
        $lm = LunarMission::find($id);

        if (!$lm) {
            return response()->json(json_decode(self::NOT_FOUND));
        }

        return response()->json(null, 204);
    }

    public function editLunarMission(Request $request, int $id)
    {
        $mission = LunarMission::find($id);

        if (!$mission) {
            return response()->json(json_decode(self::NOT_FOUND));
        }

        $mission->missions = $request->all();
        $mission->save();
        $response = '{
"data": {
"code": 200,
"message": "Миссия обновлена"
}
}';

        return response()->json(json_decode($response));
    }

    public function addSpaceFlight(Request $request)
    {
        $mission = new SpaceFlight();
        $mission->flight = $request->all();
        $mission->save();
        $response = '{
"data": {
"code": 201,
"message": "Космический полет создан"
}
}';

        return response()->json(json_decode($response), 201);
    }

    public function getSpaceFlight()
    {
        $flights = SpaceFlight::query()->where('flight->seats_available', '>', 0)->get();
        $flights = $flights->map(fn(SpaceFlight $flight) => $flight->flight);

        return response()->json($flights->all());
    }

    public function bookFlight(Request $request)
    {
        $number = $request->get('flight_number');
        $flight = SpaceFlight::query()->where('flight->flight_number', $number)->where('flight->seats_available', '> 0')->first();
        $response = '{
"data": {
"code": 404,
"message": "Полет не найден"
}
}';
        if (!$flight) {
            return response()->json(json_decode($response), 404);
        }

        $flightInfo = $flight->flight;
        $flightInfo['seats_available'] = $flightInfo['seats_available'] - 1;
        $flight->flight = $flightInfo;
        $flight->save();

        $response = '{
"data": {
"code": 201,
"message": "Рейс забронирован"
}
}';

        return response()->json(json_decode($response), 201);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        // для поиска по массиву crew, находящимся в объекте spacecraft
        // который в свою очередь находится в объекте mission и в папке missions используется следующий запрос
        $result = LunarMission::query()
            ->orWhereRaw("missions->'$.mission.spacecraft.crew[*].name' LIKE '%$query%'")
            ->get();
        // 'это костыль, для того чтобы подсветить, что найден пилот и чтоб не возиться с джойнами в SQL запросах
        $result2 = LunarMission::query()
            ->orWhere('missions->mission->name', 'LIKE', "%$query%")
            ->get();
        $result = $result->map(function (LunarMission $lunarMission) use ($query) {
            $mission = $lunarMission->missions;
            return [
                "type" => 'Миссия',
                "name" => $mission['mission']['name'],
                "launch_date" => $mission['mission']['launch_details']['launch_date'],
                "landing_date" => $mission['mission']['landing_details']['landing_date'],
                'crew' => $mission['mission']['spacecraft']['crew'],
                'landing_site' => $mission['mission']['landing_details']['landing_site']['name']
            ];
        });
        $result2 = $result2->map(function (LunarMission $lunarMission) use ($query) {
            $mission = $lunarMission->missions;
            return [
                "type" => "Пилот",
                "name" => $mission['mission']['name'],
                "launch_date" => $mission['mission']['launch_details']['launch_date'],
                "landing_date" => $mission['mission']['landing_details']['landing_date'],
                'crew' => $mission['mission']['spacecraft']['crew'],
                'landing_site' => $mission['mission']['landing_details']['landing_site']['name']
            ];
        });

        return response()->json(['data' => $result->merge($result2)->all()]);
    }

    private function checkAuth(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        return User::where('token', $token)->first();
    }
}
