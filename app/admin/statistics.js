// Set up the the table for DataTable
$(document).ready(function() {
  // Can't populate datatables if there is not data
  if (statisticsTableDataSource.length == 0) return;

  tableHeaders = statisticsTableDataSource[0];

  tableHeaders = tableHeaders.map(column_name => {
    return { title: column_name };
  });

  tableData = statisticsTableDataSource[1];

  $('.log-statistics').DataTable({
    data: tableData,
    columns: tableHeaders,
    stateSave: true,
    paging: false,
    fixedHeader: {
      headerOffset: 40
    }
  });
});
