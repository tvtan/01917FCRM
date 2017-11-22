<!-- Modal Receipt -->
<div class="modal fade" id="receipt" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/sale_orders/receipt/'.$customer_id.'/'.$sale_id.'/'.$type.'/'.$isDeposit,array('id'=>'receipt-form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                        <div class="col-md-12">
                        <div class="form-group">
                           <div class="radio radio-primary radio-inline">
                              <input type="radio" name="receipts" id="receipt" <?=$receipt->id_report_have? 'disabled': ''?> value="receipt" checked>
                              <label for="receipt"><?=_l('als_receipts')?></label>
                            </div>
                            <div class="radio radio-primary radio-inline">
                              <input type="radio" name="receipts" id="report_have" <?=$receipt->id_receipts? 'disabled': ''?> value="report_have" <?php if($receipt->id_report_have) echo "checked"; ?>   >
                              <label for="report_have"><?=_l('als_report_have')?></label>
                            </div>
                        </div> 
                    </div>   
                </div>
                <div class="row">
                        <div class="col-md-4">
                            <?php
                            $default_date = ( isset($receipt) ? _d($receipt->date_of_accounting) : _d(date('Y-m-d')));
                            echo render_date_input( 'date_of_accounting', 'date_of_accounting' , $default_date , 'date_of_accounting');
                            ?>
                            <?php
                            $default_date = ( isset($receipt) ? _d($receipt->day_vouchers) : _d(date('Y-m-d')));
                            echo render_date_input( 'day_vouchers', 'day_vouchers' , $default_date , 'day_vouchers');
                            ?>
                            
                        </div>
                        <div class="col-md-4">
                            <div class="code_vouchers1 hide" ><?=$code_vouchers?></div>
                            <div class="code_vouchers2 hide" ><?=$code_vouchers2?></div>
                            <?php
                            $code_vouchers = (isset($receipt) ? $receipt->code_vouchers : $code_vouchers);
                            echo render_input('code_vouchers', 'code_noo',$code_vouchers,'text',array('readonly'=>true));
                            // ,'text',array('readonly'=>'readonly')
                            ?>
                            <?php
                            $default_date = ( isset($receipt) ? _d($receipt->date_create) : _d(date('Y-m-d')));
                            echo render_date_input( 'date_create', 'project_datecreated' , $default_date , array(),array(),'hide');
                            ?>
                            <?php
                              $receiver = (isset($receipt) ? $receipt->receiver : "");
                              echo  render_input('receiver', _l('_receiver'), $receiver);
                              // ,'text',array('onkeyup'=>"formatNumBerKeyUp('receiver')")
                              ?>
                        </div>
                        <div class="col-md-4">
                            <?php $selected_client = (isset($receipt) ? ($receipt->id_client?$receipt->id_client:$customer_id) : $customer_id);?>
                            <?php $client_disabled = (isset($receipt) ? array('disabled'=>$disabled) : array());?>
                            <?php echo render_select('id_client',$client,array('userid','company'),_('lblclient'),$selected_client,$client_disabled); ?>

                             <?php
                            $selected = ( isset($receipt) ? $receipt->id_account_person : "");
                            echo render_select('id_account_person',$account_person,array('id','account','name_bank'),'account_person',$selected,array(),array());
                            ?>

                            
                          <?php
                              $address = (isset($receipt) ? $receipt->address : "");
                              echo  render_input('address', _l('note'), $address);
                          ?>

                          <?=form_hidden('isDeposit',($isDeposit))?>


                              
                        </div>
                        <div class="col-md-8">
                            <?php
                                  $reason = (isset($receipt) ? $receipt->reason : $title);
                                  echo  render_input('reason', _l('reason'), $reason);
                                  $style='style="display: none;"';
                              ?>
                        </div>
                          <div class="col-md-4">
                          <?php
                            $payment_modes=array(
                                array('id'=>'Chuyển khoản','name'=>'Chuyển khoản'),
                                array('id'=>'Thẻ','name'=>'Thẻ')
                            );
                            $payment_mode = (isset($report_have) ? $report_have->payment_mode : '');
                            echo render_select('payment_mode',$payment_modes,array('id','name'),'payment_mode',$payment_mode);
                            ?>

                             </div>
                </div>
                <div class="row" >
                    <div class="col-md-12">
                        <div class="panel-body mtop10">
                        <div class="table-responsive s_table">
                            <table class="table items item-export no-mtop">
                                <thead>
                                    <tr>
                                        <th <?=$style?> class="text-left"><?php echo _l('Diễn giải'); ?></th>
                                        <th <?=$style?> class="text-left"><?php echo _l('tk_no'); ?></th>
                                        <th <?=$style?> class="text-left"><?php echo _l('tk_co'); ?></th>
                                        <th class="text-left"><?php echo _l('code_noo'); ?></th>
                                        <th class="text-left"><?php echo _l('money'); ?></th>
                                        <th <?=$style?> class="text-left"><?php echo _l('discount'); ?></th>
                                        <th class="text-left"><?php echo _l('total_money_payment'); ?></th>
                                        <th <?=$style?> class="text-left"><?php echo _l('tk_ck'); ?></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!$receipt){ ?>  

                                    <tr class="sortable item">
                                        <td <?=$style?>>
                                            <?php $i=0;?>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="text" name="items[<?php echo $i ?>][note]" id="note" class="form-control" value="<?php echo $reason; ?>">
                                            </div>
                                        </td>
                                        <td <?=$style?>>
                                            <select class="selectpicker" data-live-search="true" name="items[<?php echo $i ?>][tk_no]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_no')?>">
                                                <?php if($tk_no){?>
                                                    <option></option>
                                                    <?php foreach($tk_no as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==2){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td <?=$style?>>
                                            <select class="selectpicker" data-live-search="true" name="items[<?php echo $i ?>][tk_co]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_co')?>">
                                                <?php if($tk_co){?>
                                                    <option></option>
                                                    <?php foreach($tk_co as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==6){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                            
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="items[<?php echo $i ?>][sales]" <?=$disabled?> data-width="100%" data-none-selected-text="<?php echo _l('code_noo')?>">
                                                <?php
                                                $sale_type=($this->uri->segment(2)=='sale_orders'? '-PO':'-SO');
                                                if($type=='PO')
                                                {
                                                    $get_sales=$this->receipts_model->get_table_id('tblsale_orders','id='.$sale_id);
                                                    $selected='selected';
                                                }
                                                else
                                                {
                                                    $get_sales=$this->receipts_model->get_table_id('tblsales','id='.$sale_id);
                                                    $selected='selected';
                                                }
                                                if($get_sales){?>
                                                    <?php
                                                     $last_code =$get_sales->prefix.$get_sales->code;
                                                    ?>
                                                    <option value="<?=$sale_id.$sale_type?>" <?=$selected?>><?=$last_code?></option>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input  readonly type="text" name="items[<?php echo $i ?>][total]" class="form-control _total" value="<?=number_format($get_sales->total)?>">
                                            </div>
                                        </td>
                                        <td <?=$style?>>
                                            <div class="form-group">
                                                <input readonly type="text" name="items[<?php echo $i ?>][discount]" class="form-control _discount" value="<?=number_format($get_sales->discount)?>">

                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="text" name="items[<?php echo $i ?>][subtotal]" class="form-control _subtotal" onkeyup="formatNumBerKeyUp(this)" value="<?=number_format(getTotalPartPayment($sale_id))?>">
                                            </div>
                                        </td>
                                        <td <?=$style?>>
                                            <select class="selectpicker" data-live-search="true" name="items[<?php echo $i ?>][tk_ck]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_ck')?>">
                                                <?php if($tk_ck){?>
                                                    <option></option>
                                                    <?php foreach($tk_ck as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==193){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        
                                    </tr>

                                    <?php } else{ ?>

                                        <tr class="sortable item">
                                        <td <?=$style?>>
                                            <?php $i=0;?>
                                            <div class="form-group">
                                                <input type="hidden" name="item[<?php echo $i; ?>][id]" value="<?php echo $receipt->id; ?>">
                                                <?php 
                                                $name='id_receipts';
                                                $id=$receipt->id_receipts;
                                                if($receipt->id_report_have) 
                                                {
                                                    $name='id_report_have';
                                                    $id=$receipt->id_report_have;
                                                }
                                                $disabled='disabled';
                                                ?>
                                                <input type="hidden" name="<?=$name?>" value="<?php echo $id; ?>">
                                                <input <?=$readonly?> type="text" name="item[<?php echo $i ?>][note]" id="note" class="form-control" value="<?php echo $receipt->note; ?>">
                                            </div>
                                        </td>
                                        <td <?=$style?>>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][tk_no]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_no')?>">
                                                <?php if($tk_no){?>
                                                    <option></option>
                                                    <?php foreach($tk_no as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$receipt->tk_no){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td <?=$style?>>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][tk_co]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_co')?>">
                                                <?php if($tk_co){?>
                                                    <option></option>
                                                    <?php foreach($tk_co as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$receipt->tk_co){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                            
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][sales]" <?=$disabled?> data-width="100%" data-none-selected-text="<?php echo _l('code_noo')?>">
                                                <?php
                                                $id=explode('-',$receipt->sales);

                                                if(!$receipt->sales) $id=explode('-',$receipt->contract);

                                                if($id[1]=='PO')
                                                {
                                                    $get_sales=$this->receipts_model->get_table_id('tblsale_orders','id='.$id[0]);
                                                    $selected='selected';
                                                }
                                                else
                                                {
                                                    $get_sales=$this->receipts_model->get_table_id('tblsales','id='.$id[0]);
                                                }

                                                if($get_sales){?>
                                                    <?php
                                                     $last_code =$get_sales->prefix.$get_sales->code;
                                                    ?>
                                                    <option value="<?=$receipt->sales?>" <?=$selected?>><?=$last_code?></option>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input  readonly type="text" name="item[<?php echo $i ?>][total]" class="form-control _total" value="<?=number_format($receipt->total?$receipt->total:$get_sales->total)?>">
                                            </div>
                                        </td>
                                        <td <?=$style?>>
                                            <div class="form-group">
                                                <input readonly type="text" name="item[<?php echo $i ?>][discount]" class="form-control _discount" value="<?=number_format($receipt->discount)?>">

                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="number" min="0" name="item[<?php echo $i ?>][subtotal]" class="form-control _subtotal" onkeyup="formatNumBerKeyUp(this)" value="<?=number_format($receipt->subtotal)?>">
                                            </div>
                                        </td>
                                        <td <?=$style?>>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][tk_ck]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_ck')?>">
                                                <?php if($tk_ck){?>
                                                    <option></option>
                                                    <?php foreach($tk_ck as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$receipt->tk_ck){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#receipt-form"><?php echo _l('submit'); ?></button>
        </div>
        <?php echo form_close(); ?>
        </div>
    </div>
</div>
