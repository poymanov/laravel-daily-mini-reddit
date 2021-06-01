<?php

declare(strict_types=1);

namespace Tests\Feature\Report;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ссылка на жалобу недоступна гостям
     */
    public function testGuest()
    {
        $community = $this->createCommunity();

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertDontSee('Report Community');
    }

    /**
     * Ссылка на жалобу недоступна пользователям с неподтвержденным email
     */
    public function testNotVerifiedUser()
    {
        $community = $this->createCommunity();

        $this->signIn($this->createUser([], true));

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertDontSee('Report Community');
    }

    /**
     * Ссылка на жалобу недоступна автору комментария
     */
    public function testOwner()
    {
        $user = $this->createUser();

        $this->signIn($user);

        $community = $this->createCommunity(['user_id' => $user->id]);

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertDontSee('Report Community');
    }

    /**
     * Ссылка на жалобу успешно отображается
     */
    public function testSuccess()
    {
        $this->signIn($this->createUser());

        $community = $this->createCommunity();

        $response = $this->get($this->buildShowUrl($community->slug));
        $response->assertSee('Report Community');
    }

    /**
     * Формирование пути для просмотра сообщества
     *
     * @param string $communitySlug
     *
     * @return string
     */
    protected function buildShowUrl(string $communitySlug): string
    {
        return '/communities/' . $communitySlug;
    }
}
