<?php

declare(strict_types=1);

namespace Tests\Feature\Post;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Голосование недоступно для гостей
     */
    public function testGuest()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug));
        $response->assertRedirect(self::LOGIN_URL);
    }

    /**
     * Голосование недоступно для пользователей с неподтвержденным email
     */
    public function testVerifiedUser()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn(User::factory()->unverified()->create());
        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug));
        $response->assertRedirect(self::VERIFY_EMAIL_URL);
    }

    /**
     * Голосование недоступно для публикации из несуществующего сообщества
     */
    public function testNotExistedCommunity()
    {
        $post = $this->createPost();

        $this->signIn();
        $response = $this->post($this->buildVoteUrl('test', $post->slug));
        $response->assertNotFound();
    }

    /**
     * Голосование недоступно для публикации из удаленного сообщества
     */
    public function testDeletedCommunity()
    {
        $community = $this->createCommunity([], true);
        $post      = $this->createPost(['community_id' => $community->id]);

        $this->signIn();
        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug));
        $response->assertNotFound();
    }

    /**
     * Голосование недоступно для несуществующей публикации
     */
    public function testNotExistedPost()
    {
        $community = $this->createCommunity();

        $this->signIn();
        $response = $this->post($this->buildVoteUrl($community->slug, 'test'));
        $response->assertNotFound();
    }

    /**
     * Голосование недоступно для удаленной публикации
     */
    public function testDeletedPost()
    {
        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id], true);

        $this->signIn();
        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug));
        $response->assertNotFound();
    }

    /**
     * Голосование недоступно для собственной публикации
     */
    public function testOwnerPost()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user->id]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug), ['vote' => 1]);
        $response->assertForbidden();
    }

    /**
     * Голосование невозможно без параметра голосования
     */
    public function testWithoutVoteParameter()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user->id]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug));
        $response->assertSessionHasErrors('vote');

        $this->assertDatabaseCount('post_votes', 0);
    }

    /**
     * Голосование невозможно с неправильным параметром голосования
     */
    public function testWrongVoteParameter()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id, 'user_id' => $user->id]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug), ['vote' => 'test']);
        $response->assertSessionHasErrors('vote');

        $this->assertDatabaseCount('post_votes', 0);
    }

    /**
     * Успешное голосование
     */
    public function testSuccess()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug), ['vote' => 1]);
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote'    => 1,
        ]);
    }

    /**
     * Успешное повторное голосование с другим параметром голосования
     */
    public function testSuccessNewVoteParameter()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostVote(['post_id' => $post->id, 'user_id' => $user->id, 'vote' => -1]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug), ['vote' => 1]);
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseCount('post_votes', 1);

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote'    => 1,
        ]);

        $this->assertDatabaseMissing('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote'    => -1,
        ]);
    }

    /**
     * Успешное повторное голосование с таким же параметром голосования
     */
    public function testSuccessSameVoteParameter()
    {
        $user = $this->createUser();
        $this->signIn($user);

        $community = $this->createCommunity();
        $post      = $this->createPost(['community_id' => $community->id]);
        $this->createPostVote(['post_id' => $post->id, 'user_id' => $user->id, 'vote' => 1]);

        $response = $this->post($this->buildVoteUrl($community->slug, $post->slug), ['vote' => 1]);
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('alert.success');

        $this->assertDatabaseCount('post_votes', 1);

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote'    => 1,
        ]);
    }

    /**
     * Формирование пути для голосования
     *
     * @param string $communitySlug
     * @param string $postSlug
     *
     * @return string
     */
    protected function buildVoteUrl(string $communitySlug, string $postSlug): string
    {
        return '/communities/' . $communitySlug . '/posts/' . $postSlug . '/votes';
    }
}
