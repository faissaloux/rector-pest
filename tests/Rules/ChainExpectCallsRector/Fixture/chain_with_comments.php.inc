<?php

expect($value)->toBeInt();
expect($value)->toBe(10);
// some comment
expect($value)->toBeGreaterThan(5);

-----
<?php

expect($value)->toBeInt()->toBe(10);
// some comment
expect($value)->toBeGreaterThan(5);
