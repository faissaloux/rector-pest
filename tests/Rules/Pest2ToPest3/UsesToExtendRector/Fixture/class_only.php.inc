<?php

use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTestCase;

// Class should be converted to extend()
pest()->uses(SampleTestCase::class);

?>
-----
<?php

use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTestCase;

// Class should be converted to extend()
pest()->extend(SampleTestCase::class);

?>
