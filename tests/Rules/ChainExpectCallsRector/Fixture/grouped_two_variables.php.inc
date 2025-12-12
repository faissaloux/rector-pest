<?php

test('grouped variables chaining', function () {
    $a = 10;
    $b = 20;

    expect($a)->toBe(10);
    expect($a)->toBeInt();
    expect($b)->toBe(10);
    expect($b)->toBeInt();
});

?>
-----
<?php

test('grouped variables chaining', function () {
    $a = 10;
    $b = 20;

    expect($a)->toBe(10)->toBeInt()->and($b)->toBe(10)->toBeInt();
});

?>
