<?php

$variables = [
    "TEST 0" => 0,
    "TEST 1" => 42,
    "TEST 2" => 4.2,
    "TEST 3" => .42,
    "TEST 4" => 42.,
    "TEST 5" => "42",
    "TEST 6" => "a42",
    "TEST 7" => "42a",
    "TEST 8" => 0x24,
    "TEST 9" => 1337e0
];

# Check if your variable is an integer

foreach ($variables as $key => $value) {
    // $arr[3] wird mit jedem Wert von $arr aktualisiert...


    if ( !is_int($value) || !ctype_digit($value) ) {
         echo "[ctype]: Your variable is not an integer. Key: " . $key . " Value: " . $value . "<br>";
    }

    if ( filter_var($value, FILTER_VALIDATE_INT) == FALSE ) {
        echo "[FILTER_VALIDATE_INT]: Your variable is not an integer. Key: " . $key . " Value: " . $value . "<br>";
    }

}

?>