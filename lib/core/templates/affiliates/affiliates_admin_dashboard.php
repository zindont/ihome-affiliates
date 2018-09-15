<?php
/**
 * 
 * Copyright (c) 2010, 2011 Linh Ân https://zindo.info
 * 
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Ân
 * @package affiliates
 * @since affiliates 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}
$periodDate = 30;
$dashboard_data = Affiliates_Utility::getOverviewInLastNumbersDate($periodDate);
$chart_data = Affiliates_Utility::getChartDataInLastNumbersDate($periodDate);
?>

<div class="container-fluid no-padding">
	<?php echo '<h1>' . __( 'Tổng quan Affiliates', 'affiliates' ) . '</h1>'; ?>
    <?php echo '<h2>' . __( '30 Ngày gần nhất', 'affiliates' ) . '</h2>' ?>
	<div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo esc_attr( Affiliates_Utility::price_format($dashboard_data['total_billing']) ) ?></div>
                            <div class="text-uppercase"><?php _e( 'Chuyển đổi phát sinh', 'affiliates' ) ?></div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left"><?php _e( 'Xem chi tiết', 'affiliates' ) ?></span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-credit-card fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo esc_attr( Affiliates_Utility::price_format($dashboard_data['total_commision']) ) ?></div>
                            <div class="text-uppercase"><?php _e( 'Hoa hồng phát sinh', 'affiliates' ) ?></div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left"><?php _e( 'Xem chi tiết', 'affiliates' ) ?></span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-calendar-times-o fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo esc_attr( Affiliates_Utility::price_format($dashboard_data['total_cancelled_commission']) ) ?></div>
                            <div class="text-uppercase"><?php _e( 'Hoa hồng đã hủy', 'affiliates' ) ?></div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <span class="pull-left"><?php _e( 'Xem chi tiết', 'affiliates' ) ?></span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> <span><?php _e( 'Biểu đồ', 'affiliates' ) ?></span>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div id="morris-area-chart"></div>
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
    </div>    
</div>

<!-- Morris data -->
<script type="text/javascript">
    var chart_data = <?php echo $chart_data ?>;
</script>