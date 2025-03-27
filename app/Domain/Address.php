<?php

namespace app\Domain;

class Address
{
    public function __construct(
        public readonly bool $send_flag,
        public readonly $work_zipCode,
        public readonly $work_prefecture,
        public readonly $work_address,
        public readonly $zipCode,
        public readonly $prefecture,
        public readonly $address,
    )
    {
        //;
    }
}