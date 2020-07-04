<?php

namespace Tests\Integration\Repositories;

use App\Contracts\Repositories\TransactionRepositoryContract;
use App\Models\AccessToken;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TransactionRepositoryContract $transactionRepository;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->transactionRepository = new TransactionRepository();
    }

    public function testFindAllBetweenDateForUserInScopeWhere()
    {
        $user = factory(User::class)->create();
        /** @var Tag $tag */
        $tag = factory(Tag::class)->create([
            'user_id' => $user->id,
        ]);

        $token = factory(AccessToken::class)->create();

        $account = factory(Account::class)->create([
            'access_token_id' => $token->id,
        ]);

        $tag->transactions()->create([
            'name' => 'Transaction',
            'is_possible_subscription' => false,
            'is_subscription' => false,
            'amount' => 32.10,
            'account_id' => $account->account_id,
            'date' => Carbon::create(2020, 4, 10),
            'pending' => mt_rand(0, 1),
            'category_id' => Category::all()->random()->first()->category_id,
            'transaction_id' => Str::random(32),
            'transaction_type' => 'debit',
        ]);
        $transaction1 = $tag->transactions()->create([
            'name' => 'Transaction 1',
            'is_possible_subscription' => false,
            'is_subscription' => false,
            'amount' => 32.10,
            'account_id' => $account->account_id,
            'date' => Carbon::create(2020, 4, 11),
            'pending' => mt_rand(0, 1),
            'category_id' => Category::all()->random()->first()->category_id,
            'transaction_id' => Str::random(32),
            'transaction_type' => 'debit',
        ]);
        $transaction2 = $tag->transactions()->create([
            'name' => 'Transaction 2',
            'is_possible_subscription' => false,
            'is_subscription' => false,
            'amount' => 32.10,
            'account_id' => $account->account_id,
            'date' => Carbon::create(2020, 4, 12),
            'pending' => mt_rand(0, 1),
            'category_id' => Category::all()->random()->first()->category_id,
            'transaction_id' => Str::random(32),
            'transaction_type' => 'debit',
        ]);
        $transaction3 = $tag->transactions()->create([
            'name' => 'Transaction 3',
            'is_possible_subscription' => false,
            'is_subscription' => false,
            'amount' => 32.10,
            'account_id' => $account->account_id,
            'date' => Carbon::create(2020, 4, 13),
            'pending' => mt_rand(0, 1),
            'category_id' => Category::all()->random()->first()->category_id,
            'transaction_id' => Str::random(32),
            'transaction_type' => 'debit',
        ]);
        $tag->transactions()->create([
            'date' => Carbon::create(2020, 4, 14),
            'account_id' => $account->account_id
        ]);

        $transactions = $this->transactionRepository->findAllBetweenDateForUserInScope($user, Carbon::create(2020, 4, 11), Carbon::create(2020, 4, 13), $tag->id);
        $this->assertCount(3, $transactions);
        $this->assertSame($transaction1->id, $transactions[0]->id);
        $this->assertSame($transaction2->id, $transactions[1]->id);
        $this->assertSame($transaction3->id, $transactions[2]->id);
    }
}