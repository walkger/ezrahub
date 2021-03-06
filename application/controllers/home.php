<?php

class Home_Controller extends Base_Controller {

	public function action_index() {
		$threads = Thread::order_by('sticky', 'desc')->order_by('updated_at', 'desc')->take(Config::get('ezrahub.num_homepage_threads'))->get();
		Section::inject('title', 'a forum for Cornell University students');
		Section::inject('description', 'Ezra Hub is a popular and student-run forum for Cornell University students. Anonymous posting and user accounts are allowed and everything from frats, sororities, classes, drugs, housing and more is discussed. Ezra Hub is not endorsed by Cornell University.');
        $max_pages = ceil(Thread::count() / (int)Config::get('ezrahub.num_homepage_threads'));
		if (empty($threads)) {
			$this->layout->nest('content', 'home.nothreadsyet');
		} else {
			$this->layout->nest('content', 'home.index', array('threads' => $threads, 'page_number' => 1, 'max_pages' => $max_pages));
		}
	}

    public function action_page($page_number) {
        $max_pages = ceil(Thread::count() / (int)Config::get('ezrahub.num_homepage_threads'));
        if ($page_number < 1 || $page_number > $max_pages) {
            //can't have a negative page number, or page 0, or page greater than there is available
            return Response::error('404');
        } elseif ($page_number == 1) {
            $threads = Thread::order_by('sticky', 'desc')->order_by('updated_at', 'desc')->take(Config::get('ezrahub.num_homepage_threads'))->get();
            Section::inject('title', 'a forum for Cornell University students');
            Section::inject('description', 'Ezra Hub is a popular and student-run forum for Cornell University students. Anonymous posting and user accounts are allowed and everything from frats, sororities, classes, drugs, housing and more is discussed. Ezra Hub is not endorsed by Cornell University.');
            if (empty($threads)) {
                $this->layout->nest('content', 'home.nothreadsyet');
            } else {
                $this->layout->nest('content', 'home.index', array('threads' => $threads, 'page_number' => 1, 'max_pages' => $max_pages));
            }
        } else {
            //skip all threads on pages before this one
            $multiplier = $page_number - 1;
            $threads = Thread::order_by('sticky', 'desc')->order_by('updated_at', 'desc')->skip($multiplier * Config::get('ezrahub.num_homepage_threads'))->take(Config::get('ezrahub.num_homepage_threads'))->get();
            Section::inject('title', 'Page ' . $page_number);
            Section::inject('description', 'Ezra Hub is a popular and student-run forum for Cornell University students. Anonymous posting and user accounts are allowed and everything from frats, sororities, classes, drugs, housing and more is discussed. Ezra Hub is not endorsed by Cornell University.');
            $this->layout->nest('content', 'home.index', array('threads' => $threads, 'page_number' => $page_number, 'max_pages' => $max_pages));
        }
    }

	public function action_rules() {
		Section::inject('title', 'Forum rules' );
		Section::inject('description', 'Ezra Hub has some simple to follow rules, find out what they are to avoid being banned.');
		$this->layout->nest('content', 'home.rules');
	}

	public function action_about() {
		Section::inject('title', 'About us and our history' );
		Section::inject('description', 'Find out about the history of Ezra Hub, from our birth as Cornell Hub, to our history with the administration and more!');
		$this->layout->nest('content', 'home.about');
	}

	public function action_banned() {
		if (Auth::check() && Auth::user()->banned) {
			$ban = Auth::user()->bans_to()->order_by('expires_at', 'desc')->first();
			$this->layout->nest('content', 'error.banned', array('ban' => $ban));
		}// elseif (IPBanUtil::banned(Request::ip)) {
    //     $ban = IPBanUtil::getBan(Request::ip);
    // }
		elseif (!isset($ban)) {
    		header("Location: {$_SERVER['HTTP_HOST']}");
    	}
	}

}
