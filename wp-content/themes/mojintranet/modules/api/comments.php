<?php if (!defined('ABSPATH')) die();

class Comments_API extends API {
  public function __construct($params) {
    parent::__construct();
    $this->parse_params($params);
    $this->route();
  }

  protected function route() {
    switch ($this->get_method()) {
      case 'GET':
        $this->read();
        break;

      case 'PUT':
        $this->update();

      default:
        break;
    }
  }

  protected function parse_params($params) {
    $root_comment_id = (int) $params[1] ?: 0;

    $this->params = array(
      'post_id' => $params[0],
      'root_comment_id' => $root_comment_id,
    );

    if($root_comment_id === 0) {
      $this->params = array_merge($this->params, array(
        'last_comment_id' => $params[2],
        'per_page' => $params[3]
      ));
    }
  }

  protected function read() {
    $data = $this->MVC->model->comments->read($this->params);
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }

  protected function update() {
    $this->MVC->model('bad_words');

    //validate the form
    $val = new Validation();

    if($this->MVC->model->bad_words->has_bad_words($this->put('comment'))) {
      $val->error('comment', 'comment', '', 'bad_words');
    }

    if(strlen($this->put('comment')) > 500) {
      $val->error('comment', 'comment', 'The comment is too long');
    }

    //add comment
    if(!$val->has_errors()) {
      $data = $this->MVC->model->comments->update(
        $this->params['post_id'],
        $this->put('comment'),
        $this->put('in_reply_to_id'),
        $this->put('root_comment_id'),
        $this->put('nonce')
      );
    }
    else {
      $data['validation'] = $val->get_errors();
    }

    $data['success'] = !$val->has_errors();
    $data['url_params'] = $this->params;
    $this->response($data, 200);
  }
}
