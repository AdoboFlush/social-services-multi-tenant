<?php

namespace Tests\Feature;

use App\Repositories\Setting\SettingInterface;
use App\Repositories\Setting\SettingRepository;
use App\Repositories\WelcomeMessage\WelcomeMessageRepository;
use App\Services\Setting\SettingService;
use App\Setting;
use App\WelcomeMessage;
use Illuminate\Http\Request;
use Tests\TestCase;

class WelcomeMessageTest extends TestCase
{
    public function testShouldReplaceMemberNamePlaceholder()
    {
        /**
         * @TODO add acting users (business, admin and personal)
         *
         * - verify actual welcome message content
         */
        $settingRepository = new SettingRepository(new Setting());
        $welcomeMessageRepository = new WelcomeMessageRepository(new WelcomeMessage());
        $settings = new SettingService($welcomeMessageRepository, $settingRepository);
        $request= new Request();

        $request->replace([
            'content' => '{MEMBER_NAME}',
        ]);

        $result = $settings->updateWelcomeMessage($request);
        $session = $result->getSession();

        $this->assertTrue($session->has('message'));

        $this->assertTrue($session->get('message') === 'Welcome Message updated!');
    }
}
