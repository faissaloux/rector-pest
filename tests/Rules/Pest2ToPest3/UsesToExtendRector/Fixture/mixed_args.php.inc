<?php

use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTestCase;
use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTrait;

// Mixed class and trait should be split
pest()->uses(SampleTestCase::class, SampleTrait::class)->in('Feature');

?>
-----
<?php

use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTestCase;
use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTrait;

// Mixed class and trait should be split
pest()->extend(SampleTestCase::class)->use(SampleTrait::class)->in('Feature');

?>
