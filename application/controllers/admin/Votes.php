<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Votes extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('votes_model');
    }
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('votes');
        }
        $data['title'] = _l('votes');
        $this->load->view('admin/votes/manage', $data);
    }
    public function votes($id="")
    {
        if($this->input->post())
        {
            if($id=="")
            {
                if (!has_permission('cocvachitrancc', '', 'create')) {
                    access_denied('cocvachitrancc');
                }
                $data=$this->input->post();
                $_data=$data['items'];
                unset($data['items']);
                $data['id_staff']=get_staff_user_id();
                $id=$this->votes_model->insert($data);
                if($id) {
                    $_id = $this->votes_model->insert_votes_contract($id, $_data);
                    if($_id)
                    {
                        set_alert('success', _l('added_successfuly', _l('votes')));
                        redirect(admin_url('votes/votes/'.$id));
                    }
                    else
                    {
                        set_alert('danger', _l('problem_adding'));
                        redirect(admin_url('votes/votes/'.$id));
                    }
                }
                else
                {
                    set_alert('danger', _l('problem_adding'));
                    redirect(admin_url('votes/votes'));
                }
            }
            else
            {
                if (!has_permission('cocvachitrancc', '', 'edit')) {
                    access_denied('cocvachitrancc');
                }
                $data=$this->input->post();
                $_data['item']=$data['item'];
                $_data['items']=$data['items'];
                unset($data['item']);
                unset($data['items']);
                $result=$this->votes_model->update($id,$data);
                $_result=$this->votes_model->update_votes_cotract($id,$_data['item']);
                $__result=$this->votes_model->insert_votes_contract($id,$_data['items']);
                if($result||$_result||$__result)
                {
                    set_alert('success', _l('updated_successfuly'));
                }
                redirect(admin_url('votes/votes/'.$id));


            }

        }
        else
        {
            if($id=="")
            {
                if (!has_permission('cocvachitrancc', '', 'create')) {
                    access_denied('cocvachitrancc');
                }
                $data['heading']=_l('votes_add_heading');
                $data['title']=_l('votes');
                $data['supplier']=$this->votes_model->get_table_where('tblsuppliers');
                $data['purchase_contracts']=$this->votes_model->get_table_where('tblpurchase_contracts');
                $data['currencies']=$this->votes_model->get_table_where('tblcurrencies');
                $data['contract']=$this->votes_model->get_contract();
                $data['tk_no']=$this->votes_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
                $data['tk_co']=$this->votes_model->get_table_where('tblaccounts');
                $data['votes_contract']=$this->votes_model->get_votes_contract();
                $data['votes_contract_purchase']=$this->votes_model->get_votes_purchase();
                $data['code_vouchers']=$this->votes_model->get_vouchers();
            }
            else
            {
                if (!has_permission('cocvachitrancc', '', 'edit')) {
                    access_denied('cocvachitrancc');
                }
                $data['heading']=_l('votes_update_heading');
                $data['title']=_l('votes');
                $data['supplier']=$this->votes_model->get_table_where('tblsuppliers');
                $data['purchase_contracts']=$this->votes_model->get_table_where('tblpurchase_contracts');
                $data['currencies']=$this->votes_model->get_table_where('tblcurrencies');
                $data['contract']=$this->votes_model->get_contract();
                $data['tk_no']=$this->votes_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
                $data['tk_co']=$this->votes_model->get_table_where('tblaccounts');
                $data['votes_contract']=$this->votes_model->get_votes_contract();
                $data['votes_contract_purchase']=$this->votes_model->get_votes_purchase();

                $data['supplier']=$this->votes_model->get_table_where('tblsuppliers');
                $data['vote']=$this->votes_model->get_index_votes($id);
                if($data['vote'])
                {
                   $data['votes']= $this->votes_model->index_votes_contract($id);
                }
                else
                {
                    set_alert('warning', _l('no_data_found', _l('votes')));
                    redirect(admin_url('votes/votes'));
                }
            }
            $data['title']="Phiếu chi";
            $this->load->view('admin/votes/detail',$data);
        }
    }
    public function pdf($id="")
    {
        $votes = $this->votes_model->get_data_pdf($id);
        if ($this->input->get('combo')) {
            $votes->combo=$this->input->get('combo');
        }
        $pdf      = votes_pdf($votes);
        $type     = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($votes->code_vouchers) . '.pdf', $type);
    }


    public function update_status()
    {
        if (!has_permission('cocvachitrancc', '', 'approve_all')) {
            if (!has_permission('cocvachitrancc', '', 'approve_departments')) {
                access_denied('cocvachitrancc');
            }
        }
        $id=$this->input->post('id');
        $status=$this->input->post('status');


        $staff_id=get_staff_user_id();
        $date=date('Y-m-d H:i:s');
        $data=array('status'=>$status);

        $inv=$this->votes_model->get_index_votes($id);

        if(is_admin() && $status==0)
        {
            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;

            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;

            $data['status']=2;
        }
        elseif(is_admin() && $status==1)
        {
            $data['status']=2;
                $data['staff_browse']=$staff_id;
                $data['date_status']=$date;
        }
        elseif(can_update_staff($inv->id_staff))
        {
            $data['status']+=1;
            $data['staff_browse']=$staff_id;
            $data['date_status']=$date;
        }
        $success=false;

        if(is_admin() || can_update_staff($inv->id_staff))
        {
            $success=$this->votes_model->update_status($id,$data);
        }

        if($success) {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Xác nhận đề xuất thành công')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }
}
