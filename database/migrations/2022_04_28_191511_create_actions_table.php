<?php

use App\Models\Action;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Admin::class)->index();
            $table->foreignIdFor(Admin::class, 'authenticator_id')
                ->nullable()
                ->index();
            $table->foreignIdFor(User::class)
                ->nullable()
                ->index();
            $table->enum('type', Action::TYPE)->index();
            $table->json('data');
            $table->enum('status', Action::STATUS)
                ->default(Action::STATUS['PENDING'])
                ->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
};
