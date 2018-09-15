jQuery(document).ready(function($) {
    Morris.Area({
        element: 'morris-area-chart',
        data: chart_data,
        xkey: 'period',
        ykeys: ['total_billing', 'total_commision', 'total_cancelled_commission'],
        dateFormat: function(x){
            return $.datepicker.formatDate('dd/m', new Date(x * 1000));
        },
        xLabelFormat: function(x){
			return $.datepicker.formatDate('dd/m', new Date(x * 1000));
        },
        labels: ['Chuyển đổi phát sinh', 'Hoa hồng phát sinh', 'Hoa hồng đã hủy'],
        behaveLikeLine: true,
        pointSize: 2,
        lineColors: ["#337ab7", "#5cb85c", "#d9534f", "#afd8f8", "#edc240", "#cb4b4b", "#9440ed"],
        postUnits: ' đ',
        hideHover: 'auto'
    });
});	