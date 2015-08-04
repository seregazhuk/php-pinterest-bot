<?php if ( ! defined('BASEPATH'))  { exit('No direct script access allowed'); }

/**
 * Class Payouts
 * @property $payout_requests_model	Payout_requests_model
 * @property $pagination
 * @property $billing_history_model Billing_history_model
 * @property $user_model			Users_model
 * @property $wallet_model			Wallet_model
 */
class Payouts extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('payout_requests_model');
        $this->load->model('wallet_model');
    }

    /**
     * Партнерские выплаты: В ожидании
     *
     * @param int $page	Страница пагинации
     */
    public function index($page = 0)
    {
        $this->_show_payouts(Payout_requests_model::PAYOUT_REQUEST_STATUS_PENDING,
            site_url('payouts/index'),
            $page);
    }

    /**
     * Партнерские выплаты: Исполненные
     *
     * @param int $page	Страница пагинации
     */
    public function done($page = 0)
    {

        $this->_show_payouts(Payout_requests_model::PAYOUT_REQUEST_STATUS_DONE,
            site_url('payouts/done'),
            $page);
    }

    /**
     * Партнерские выплаты: Отмененные
     *
     * @param int $page	Страница пагинации
     */
    public function charged($page = 0)
    {
        $this->_show_payouts(Payout_requests_model::PAYOUT_REQUEST_STATUS_CHARGED,
            site_url('payouts/charged'),
            $page);
    }


    /**
     * Показывает список выплат по заданному статусу
     *
     * @param int		$status
     * @param string	$base_url
     * @param int		$page
     * @param int		$limit
     */
    protected function _show_payouts($status, $base_url, $page = 0, $limit = 10)
    {
        $data['title']               	= 'Партнерские выплаты';
        $data['active_menu']['payouts'] = TRUE;
        $data['status']					= $status;

        $data = array_merge($data, $this->_get_payouts($base_url, $status, $limit, $page));
        //Load pagination library for creating page navigation links
        $this->load->view('header-view', $data);
        $this->load->view('payouts/index-view', $data);
        $this->load->view('footer-view');
    }

    /**
     * Получить текущую выборку по истории выплат
     *
     * @param string $base_url
     * @param int $status
     * @param int $limit
     * @param int $offset
     * @return array
     */
    protected function _get_payouts($base_url, $status, $limit, $offset)
    {
        $data['payouts'] = $this->payout_requests_model
            ->query()
            ->select('payout_requests.id,
					  payout_requests.user_id,
					  sum,
					  dt_add,
					  dt_end,
					  account_id,
					  account_type,
					  email')
            ->join('users', 'users.id = payout_requests.user_id', 'inner')
            ->join('wallets', 'wallets.id = payout_requests.wallet_id', 'inner')
            ->where(array('status'=>$status))
            ->limit($limit)
            ->offset($offset)->all();

        $total_payouts = $this->payout_requests_model->query()->where(array('status'=>$status))->count();
        $data['pages'] = $this->_get_pagination($base_url, $total_payouts, $limit);


        return $data;
    }

    /**
     * Получить пагинацию для контроллера
     *
     * @param string	$base_url
     * @param int		$total_rows
     * @param int		$limit
     * @param int		$uri_segment
     * @return string
     */
    protected function _get_pagination($base_url, $total_rows, $limit, $uri_segment = 3)
    {
        $config = array(
            'base_url'        => $base_url,
            'uri_segment'     => $uri_segment,
            'total_rows'      => $total_rows,
            'per_page'        => $limit,
            'first_link'      => 'Первая',
            'last_link'       => 'Последняя',
            'full_tag_open'   => '<div class="pagination"><ul>',
            'num_tag_open'    => '<li>',
            'first_tag_open'  => '<li>',
            'first_tag_close' => '</li>',
            'cur_tag_open'    => '<li class="active"><a href="#">',
            'cur_tag_close'   => '</a></li>',
            'last_tag_open'   => '<li>',
            'prev_tag_open'   => '<li>',
            'full_tag_close'  => '</ul></div>',
            'next_tag_open'   => '<li>',
            'next_tag_close'  => '</li>',
            'last_tag_close'  => '</li>'
        );
        $this->load->library('pagination');
        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    public function edit($id, $page=0)
    {
        //Set page title
        $data['title']                  = 'Управление заявками на вывод денежных средств';
        $data['active_menu']['payouts'] = TRUE;

        $payout = $this->payout_requests_model->query()
            ->select('payout_requests.id,
														payout_requests.user_id,
														sum,
														status,
														dt_add,
														payout_requests.comment,
														dt_end,
														account_id,
														account_type,
														email')
            ->join('users', 'users.id = payout_requests.user_id', 'inner')
            ->join('wallets', 'wallets.id = payout_requests.wallet_id', 'inner')
            ->where(array('payout_requests.id'=>$id))
            ->one();


        // Если выплата существуют, собираем дополнительную информацию по клиенту:
        // общие сведения аккаунта, кошельки, история денежных операци
        if ($payout)
        {

            $this->load->model('user_model');
            $this->load->model('billing_history_model');

            $user = $this->user_model->get_user($payout->user_id);

            // Обновление статуса выплаты
            if ($this->input->post())
            {
                $this->load->library('form_validation');

                $this->form_validation->set_rules('status', 'status', 'trim|required|integer');
                $this->form_validation->set_rules('comment', 'comment', 'trim|xss_clean');

                if ($this->form_validation->run())
                {
                    $condition = array('id' => $id);
                    $attributes = array('status' => $this->input->post('status'),
                        'dt_end' => date("Y-m-d H:i:s"),
                        'comment' => $this->input->post('comment')
                    );
                    $this->payout_requests_model->update_by_attributes($attributes, $condition);

                    $payout->status = $attributes['status'];
                    $payout->comment = $attributes['comment'];

                    // Если выплата была отменена, то возвращаем деньги клиенту на баланс
                    if (intval($attributes['status']) === Payout_requests_model::PAYOUT_REQUEST_STATUS_CHARGED)
                    {
                        $user->balance += $payout->sum;
                        $billing_history = $this->billing_history_model;
                        $billing_history->sum = $payout->sum;
                        $billing_history->name = 'Отмена выплаты №' . $payout->id;
                        $user->update_balance($user->balance, $billing_history);
                    }
                }
            }

            $data['user'] = $user;
            $data['wallets'] = $this->wallet_model->query()->find_all(array('user_id' => $user->id));

            // Собираем историю денежных операций
            $billing_history_page_limit = 20;
            $billing_history_data = $this->_get_user_billing_history($payout->user_id,
                $page,
                $billing_history_page_limit);

            $data['billing_history'] = $billing_history_data['data'];
            $data['billing_history_pages'] = $this->_get_pagination(site_url('payouts/edit/'. $id),
                $billing_history_data['total'],
                $billing_history_page_limit, 4);
        }


        $data['payout'] = $payout;

        $data['payout_statuses'] = array(
            Payout_requests_model::PAYOUT_REQUEST_STATUS_PENDING	=> get_payout_status_string(Payout_requests_model::PAYOUT_REQUEST_STATUS_PENDING),
            Payout_requests_model::PAYOUT_REQUEST_STATUS_DONE		=> get_payout_status_string(Payout_requests_model::PAYOUT_REQUEST_STATUS_DONE),
            Payout_requests_model::PAYOUT_REQUEST_STATUS_CHARGED	=> get_payout_status_string(Payout_requests_model::PAYOUT_REQUEST_STATUS_CHARGED),
        );



        $this->load->view('header-view', $data);
        $this->load->view('payouts/edit-view', $data);
        $this->load->view('footer-view');
    }

    /**
     * Получить историю денежных операций пользователя
     *
     * @param integer $user_id
     * @return array
     */
    public function _get_user_billing_history($user_id, $page, $limit)
    {
        $billing_history = $this->billing_history_model
            ->query()
            ->limit($limit)
            ->offset($page)
            ->where(array('user_id'=>$user_id))
            ->all();
        $total = $this->billing_history_model
            ->query()
            ->where(array('user_id'=>$user_id))
            ->count();
        return array('data' => $billing_history, 'total' => $total);
    }

}


/* End of file payouts.php */
/* Location: ./application/controllers/payouts.php */