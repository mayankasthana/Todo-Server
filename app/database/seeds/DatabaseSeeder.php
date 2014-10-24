<?php

class DatabaseSeeder extends Seeder {

	public function run()
	{
            //$adminRole = Role::create(array('title'=>'admin'));
            $adminRole = new Role;
            $adminRole->title = "admin";
            $adminRole->save();
            $memberRole = new Role;
            $memberRole->title = 'member';
            $memberRole->save();
            
            $user = new User;
            $mayank = $user;
            $user->displayName = "Mayank";
            $user->email = "mayankasthana1993@gmail.com";
            $user->save();
            $user->role()->attach($adminRole);
            
            $user = new User;
            $shivam = $user;
            $user->displayName = "Shivam";
            $user->email = "shivam@kratee.com";
            $user->save();
            $user->role()->attach($memberRole);

            $user = new User;
            $mihir = $user;
            $user->displayName = "Mihir";
            $user->email = "mihir@gmail.com";
            $user->save();
            $user->role()->attach($memberRole);

            $task = new Task;
            $task->text = "New seed task";
            $task->status = false;
            $task->creator()->associate($user);
            $task->save();
            $task->users()->saveMany(array($shivam,$mayank));
            
            $comment = New Comment;
            $comment->text = "This is another comment";
            $comment->user_id = $mihir->id;
            $comment->task_id = $task->id;
            $comment->save();
            //$task->comments()->save($comment);
            $comment = New Comment;
            $comment->text = "This is a comment";
            $comment->user_id = $mihir->id;
            $comment->task_id = $task->id;
            $comment->save();
            
            
            $task = new Task;
            $task->text = "Second Task";
            $task->creator()->associate($mayank);
            $task->save();
            $task->users()->saveMany(array($mayank,$mihir));
		Eloquent::unguard();
	}
}