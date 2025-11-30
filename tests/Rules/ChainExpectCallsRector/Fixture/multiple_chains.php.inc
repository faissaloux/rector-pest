<?php

test('multiple chains', function () {
    $value = 10;

    expect($value)->toBe(10);
    expect($value)->toBeInt();
    expect($value)->toBeGreaterThan(5);
});

?>
-----
<?php

test('multiple chains', function () {
    $value = 10;

    expect($value)->toBe(10)->toBeInt()->toBeGreaterThan(5);
});

?>
