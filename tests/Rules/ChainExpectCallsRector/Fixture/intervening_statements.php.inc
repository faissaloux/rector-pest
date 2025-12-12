<?php

test('intervening statements prevent chaining', function () {
    $a = 10;

    expect($a)->toBe(10);

    $x = 5; // some other statement in between

    expect($a)->toBeInt();
});

?>
