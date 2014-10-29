<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForeignKeys extends Migration {

    public function up() {
        Schema::table('permission_role', function(Blueprint $table) {
            $table->foreign('permission_id')->references('id')->on('permissions')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('permission_role', function(Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('role_user', function(Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('role_user', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('login_attempts', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('tasks', function(Blueprint $table) {
            $table->foreign('created_by_user_id')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('task_user', function(Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('task_user', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('comments', function(Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('comments', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                    ->onDelete('restrict')
                    ->onUpdate('restrict');
        });
        Schema::table('notifications', function(Blueprint $table) {
            $table->foreign('to_user_id')->references('id')->on('users')
                    ->onDelete('set null')
                    ->onUpdate('restrict');
        });
    }

    public function down() {
        Schema::table('permission_role', function(Blueprint $table) {
            $table->dropForeign('permission_role_permission_id_foreign');
        });
        Schema::table('permission_role', function(Blueprint $table) {
            $table->dropForeign('permission_role_role_id_foreign');
        });
        Schema::table('role_user', function(Blueprint $table) {
            $table->dropForeign('role_user_role_id_foreign');
        });
        Schema::table('role_user', function(Blueprint $table) {
            $table->dropForeign('role_user_user_id_foreign');
        });
        Schema::table('login_attempts', function(Blueprint $table) {
            $table->dropForeign('login_attempts_user_id_foreign');
        });
        Schema::table('tasks', function(Blueprint $table) {
            $table->dropForeign('tasks_created_by_user_id_foreign');
        });
        Schema::table('task_user', function(Blueprint $table) {
            $table->dropForeign('task_user_task_id_foreign');
        });
        Schema::table('task_user', function(Blueprint $table) {
            $table->dropForeign('task_user_user_id_foreign');
        });
        Schema::table('comments', function(Blueprint $table) {
            $table->dropForeign('comments_task_id_foreign');
        });
        Schema::table('comments', function(Blueprint $table) {
            $table->dropForeign('comments_user_id_foreign');
        });
        Schema::table('notifications', function(Blueprint $table) {
            $table->dropForeign('notifications_to_user_id_foreign');
        });
    }

}
