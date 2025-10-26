<?php

it('boots the Symfony kernel successfully', function () {
    $kernel = self::bootKernel();
    expect($kernel->getContainer())->not->toBeNull();
});
