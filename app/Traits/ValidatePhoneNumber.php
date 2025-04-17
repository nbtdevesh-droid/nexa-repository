<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidatesPhoneNumbers
{
    protected $countryRules = [
        '+93' => 9, //AF
        '+355' => 9, //AL
        '+213' => 9, //DZ
        '+1-684' => 10, //AS
        '+376' => 6, //AD
        '+244' => 9, //AO
        '+1-264' => 10, //AI
        '+1-268' => 10, //AG
        '+54' => 10, //AR
        '+374' => 8, //AM
        '+297' => 7, //AW
        '+61' => 9, //AU
        '+43' => 10, //AT
        '+994' => 9, //AZ
        '+1-242' => 10, //BS
        '+973' => 8, //BH
        '+880' => 10, //BD
        '+1-246' => 10, //BB
        '+375' => 9, //BY
        '+32' => 9, //BE
        '+501' => 7, //BZ
        '+229' => 9, //BJ
        '+1-441' => 10, //BM
        '+975' => 8, //BT
        '+591' => 8, //BO
        '+387' => 8, //BA
        '+267' => 8, //BW
        '+55' => 11, //BR
        '+673' => 7, //BN
        '+359' => 9, //BG
        '+226' => 8, //BF
        '+257' => 8, //BI
        '+855' => 9, //KH
        '+237' => 9, //CM
        '+1' => 10, //CA
        '+238' => 7, //CV
        '+236' => 8, //CF
        '+235' => 8, //TD
        '+56' => 9, //CL
        '+86' => 11, //CN
        '+57' => 10, //CO
        '+269' => 7, //KM
        '+242' => 9, //CG
        '+243' => 9, //CD
        '+506' => 8, //CR
        '+385' => 9, //HR
        '+53' => 8, //CU
        '+357' => 8, //CY
        '+420' => 9, //CZ
        '+45' => 8, //DK
        '+253' => 6, //DJ
        '+1-767' => 10, //DM
        '+1-809' => 10, //DO
        '+593' => 9, //EC
        '+20' => 10, //EG
        '+503' => 8, //SV
        '+240' => 9, //GQ
        '+291' => 7, //ER
        '+372' => 8, //EE
        '+251' => 9, //ET
        '+679' => 7, //FJ
        '+358' => 9, //FI
        '+33' => 9, //FR
        '+241' => 7, //GA
        '+220' => 7, //GM
        '+995' => 9, //GE
        '+49' => 10, //DE
        '+233' => 9, //GH
        '+30' => 10, //GR
        '+1-473' => 10, //GD
        '+502' => 8, //GT
        '+224' => 9, //GN
        '+245' => 7, //GW
        '+592' => 7, //GY
        '+509' => 8, //HT
        '+504' => 8, //HN
        '+36' => 9, //HU
        '+354' => 7, //IS
        '+91' => 10, //IN
        '+62' => 10, //ID
        '+98' => 10, //IR
        '+964' => 10, //IQ
        '+353' => 9, //IE
        '+972' => 9, //IL
        '+39' => 10, //IT
        '+225' => 8, //CI
        '+1-876' => 10, //JM
        '+81' => 10, //JP
        '+962' => 9, //JO
        '+7' => 10, //KZ
        '+254' => 10, //KE
        '+686' => 5, //KI
        '+850' => 10, //KP
        '+82' => 10, //KR
        '+965' => 8, //KW
        '+996' => 9, //KG
        '+856' => 9, //LA
        '+371' => 8, //LV
        '+961' => 8, //LB
        '+266' => 8, //LS
        '+231' => 7, //LR
        '+218' => 10, //LY
    ];

    public function validatePhoneNumber(Request $request)
    {
        $rules = [
            'phone' => 'required|numeric',
            'country_code' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 401);
        }

        $mobile = $request->input('phone');
        $countryCode = $request->input('country_code');

        if (!array_key_exists($countryCode, $this->countryRules)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid country code',
            ], 400);
        }

        $expectedLength = $this->countryRules[$countryCode];
        if (strlen($mobile) != $expectedLength) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid mobile number length for the provided country code',
                'expected_length' => $expectedLength,
                'provided_length' => strlen($mobile),
            ], 400);
        }

        return null; // Return null if validation passes
    }
}
