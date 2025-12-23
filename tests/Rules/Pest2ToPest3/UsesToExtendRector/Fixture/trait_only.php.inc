<?php

use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTrait;

// Trait should be converted to use()
pest()->uses(SampleTrait::class);

?>
-----
<?php

use Tests\Rules\Pest2ToPest3\UsesToExtendRector\Fixture\SampleTrait;

// Trait should be converted to use()
pest()->use(SampleTrait::class);

?>
