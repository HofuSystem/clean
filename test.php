<?php




$db_orders = DB::connection('mysql2')->table('orders')->get();
foreach ($db_orders as $db_order) {

  if (\Core\Orders\Models\Order::query()->orWhere('reference_id', $db_order->reference_id)->exists()) {
    continue;
  }

  $order = \Core\Orders\Models\Order::insert([
    'id' =>  $db_order->id,
    'reference_id' => $db_order->reference_id,
    'type' => $db_order->type,
    'status' => $db_order->status,
    'client_id' => $db_order->client_id,
    'pay_type' => $db_order->pay_type,
    'transaction_id' => $db_order->transaction_id,
    'order_status_times' => $db_order->order_status_times,
    'days_per_week' => $db_order->days_per_week,
    'days_per_month_dates' => $db_order->days_per_month_dates,
    'note' => $db_order->note,
    'coupon_id' => $db_order->coupon_id,
    'coupon_data' => $db_order->coupon_data,
    'order_price' => $db_order->order_price,
    'delivery_price' => $db_order->delivery_price,
    'total_price' => $db_order->total_price,
    'paid' => $db_order->paid,
    'is_admin_accepted' => $db_order->is_admin_accepted,
    'admin_cancel_reason' => $db_order->admin_cancel_reason,
    'wallet_used' => $db_order->wallet_used,
    'wallet_amount_used' => $db_order->wallet_amount_used,

  ]);
}

$db_orderItems = DB::connection('mysql2')->table('order_items')->get();
foreach ($db_orderItems as $db_orderItem) {

  $orderItem = \Core\Orders\Models\OrderItem::insert([
    'id' =>  $db_orderItem->id,
    'order_id' => $db_orderItem->order_id,
    'product_id' => $db_orderItem->product_id,
    'product_name' => $db_orderItem->product_name,
    'product_data' => $db_orderItem->product_data,
    'product_price' => $db_orderItem->product_price,
    'quantity' => $db_orderItem->quantity,
    'carpet_size' => $db_orderItem->carpet_size,
    'add_by_admin' => $db_orderItem->add_by_admin,
    'update_by_admin' => $db_orderItem->update_by_admin,

  ]);
}

$db_orderItemQtyUpdates = DB::connection('mysql2')->table('order_item_qty_updates')->get();
foreach ($db_orderItemQtyUpdates as $db_orderItemQtyUpdate) {

  $orderItemQtyUpdate = \Core\Orders\Models\OrderItemQtyUpdate::insert([
    'id' =>  $db_orderItemQtyUpdate->id,
    'item_id' => $db_orderItemQtyUpdate->item_id,
    'from' => $db_orderItemQtyUpdate->from,
    'to' => $db_orderItemQtyUpdate->to,
    'updater_email' => $db_orderItemQtyUpdate->updater_email,

  ]);
}

$db_orderRepresentatives = DB::connection('mysql2')->table('order_representatives')->get();
foreach ($db_orderRepresentatives as $db_orderRepresentative) {

  $orderRepresentative = \Core\Orders\Models\OrderRepresentative::insert([
    'id' =>  $db_orderRepresentative->id,
    'order_id' => $db_orderRepresentative->order_id,
    'representative_id' => $db_orderRepresentative->representative_id,
    'type' => $db_orderRepresentative->type,
    'category_date_id' => $db_orderRepresentative->category_date_id,
    'date' => $db_orderRepresentative->date,
    'to_date' => $db_orderRepresentative->to_date,
    'category_time_id' => $db_orderRepresentative->category_time_id,
    'time' => $db_orderRepresentative->time,
    'to_time' => $db_orderRepresentative->to_time,
    'lat' => $db_orderRepresentative->lat,
    'lng' => $db_orderRepresentative->lng,
    'location' => $db_orderRepresentative->location,
    'has_problem' => $db_orderRepresentative->has_problem,

  ]);
}

$db_orderTypesOfDatas = DB::connection('mysql2')->table('order_types_of_datas')->get();
foreach ($db_orderTypesOfDatas as $db_orderTypesOfData) {

  $orderTypesOfData = \Core\Orders\Models\OrderTypesOfDatum::insert([
    'id' =>  $db_orderTypesOfData->id,
    'order_id' => $db_orderTypesOfData->order_id,
    'key' => $db_orderTypesOfData->key,
    'value' => $db_orderTypesOfData->value,

  ]);
}

$db_reportReasons = DB::connection('mysql2')->table('report_reasons')->get();
foreach ($db_reportReasons as $db_reportReason) {

  $reportReason = \Core\Orders\Models\ReportReason::insert([
    'id' =>  $db_reportReason->id,
    'ordering' => $db_reportReason->ordering,

  ]);
}

$db_reportReasons_translations = DB::connection('mysql2')->table('report_reason_translations')->get();
foreach ($db_reportReasons_translations as $db_reportReason_translations) {
  try {
    \Core\Orders\Models\ReportReasonTranslation::insert([
      'name' => $db_reportReason_translations->name,
      'desc' => $db_reportReason_translations->desc,
      'report_reason_id' => $db_reportReason_translations->report_reason_id,
      'locale' => $db_reportReason_translations->locale,

    ]);
  } catch (\Throwable $th) {
    //throw $th;
  }
}

$db_orderReports = DB::connection('mysql2')->table('order_reports')->get();
foreach ($db_orderReports as $db_orderReport) {

  $orderReport = \Core\Orders\Models\OrderReport::insert([
    'id' =>  $db_orderReport->id,
    'order_id' => $db_orderReport->order_id,
    'user_id' => $db_orderReport->user_id,
    'report_reason_id' => $db_orderReport->report_reason_id,
    'desc_location' => $db_orderReport->desc_location,

  ]);
}

$db_orderInvoices = DB::connection('mysql2')->table('order_invoices')->get();
foreach ($db_orderInvoices as $db_orderInvoice) {

  $orderInvoice = \Core\Orders\Models\OrderInvoice::insert([
    'id' =>  $db_orderInvoice->id,
    'invoice_num' => $db_orderInvoice->invoice_num,
    'data' => $db_orderInvoice->data,
    'order_id' => $db_orderInvoice->order_id,
    'user_id' => $db_orderInvoice->user_id,

  ]);
}

$db_workers = DB::connection('mysql2')->table('workers')->get();
foreach ($db_workers as $db_worker) {

  $worker = \Core\Workers\Models\Worker::insert([
    'id' =>  $db_worker->id,
    'image' => $db_worker->image,
    'name' => $db_worker->name,
    'phone' => $db_worker->phone,
    'email' => $db_worker->email,
    'years_experience' => $db_worker->years_experience,
    'address' => $db_worker->address,
    'birth_date' => $db_worker->birth_date,
    'hour_price' => $db_worker->hour_price,
    'gender' => $db_worker->gender,
    'status' => $db_worker->status,
    'identity' => $db_worker->identity,
    'nationality_id' => $db_worker->nationality_id,
    'city_id' => $db_worker->city_id,

  ]);
}

$db_workerDays = DB::connection('mysql2')->table('worker_days')->get();
foreach ($db_workerDays as $db_workerDay) {

  $workerDay = \Core\Workers\Models\WorkerDay::insert([
    'id' =>  $db_workerDay->id,
    'worker_id' => $db_workerDay->worker_id,
    'date' => $db_workerDay->date,
    'type' => $db_workerDay->type,

  ]);
}
