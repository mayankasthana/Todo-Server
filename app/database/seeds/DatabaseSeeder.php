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
            $user->username = "Mayank";
            $user->email = "mayankasthana1993@gmail.com";
            $user->password = "asdfjkl;";
            $user->save();
            $user->role()->attach($adminRole);
            
            $user = new User;
            $shivam = $user;
            $user->username = "Shivam";
            $user->email = "shivam@kratee.com";
            $user->password = ";lkjfdsa";
            $user->save();
            $user->role()->attach($memberRole);

            $user = new User;
            $mihir = $user;
            $user->username = "Mihir";
            $user->email = "mihir@gmail.com";
            $user->password = "password";
            $user->save();
            $user->role()->attach($memberRole);

            $task = new Task;
            $task->text = "New seed task";
            $task->status = true;
            $task->creator()->associate($user);
            $task->save();
            $task->users()->saveMany(array($shivam,$mayank));
  
  
            $task = new Task;
            $task->text = "Second Task";
            $task->creator()->associate($mayank);
            $task->save();
            $task->users()->saveMany(array($mayank,$mihir));
		Eloquent::unguard();
	}
}