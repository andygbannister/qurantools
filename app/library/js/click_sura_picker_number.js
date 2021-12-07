function click_sura_number(suraNumber, versesInSura) {
  // find the background of the current element, so that we can set the previously
  // selected sura to that same value
  var defaultBackgroundColor = $(".tpd-skin-qt .tpd-background-content").css(
    "background-color"
  );

  // unhighlight the previously highlighted sura
  if (previouslyHighlightedSura > 0) {
    $("#q" + previouslyHighlightedSura).removeClass("selected");
  }

  // highlight the sura they have clicked and remember it
  $("#q" + suraNumber).addClass("selected");
  previouslyHighlightedSura = suraNumber;

  ayaText = "<table>";
  ayaText += "<tr>";
  ayaText +=
    "<td colspan='24'><div class='tpd-title'><a href='verse_browser.php?V=" +
    suraNumber +
    "' class='linky-dark'><b>Verses in Sura " +
    suraNumber +
    "</a></b></div></td>";
  ayaText += "</tr><tr>";

  rowCount = 0;
  for (i = 0; i <= versesInSura; i++) {
    rowCount++;
    if (i == 0) {
      ayaText +=
        "<td class='aya-picker-number' onclick=\"location.href='home.php?SEEK=" +
        suraNumber +
        "'\">All</td>";
    } else {
      ayaText +=
        "<td class='aya-picker-number' onclick=\"location.href='home.php?SEEK=" +
        suraNumber +
        ":" +
        i +
        "'\">" +
        i +
        "</td > ";
    }
    if (rowCount > 23) {
      ayaText += "</tr><tr>";
      rowCount = 0;
    }
  }

  // 	fill in any missing cells

  if (rowCount <= 17) {
    for (i = rowCount; i <= 17; i++) {
      ayaText += "<td>&nbsp;</td>";
    }
  }

  ayaText += "</tr></table>";

  $("#aya-picker-container").html(ayaText);
  $("#aya-picker-container").css("display", "block");

  // redraw the tip with new dimensions; Tipped.refresh() does not work in this
  // context for some unknown reason.
  Tipped.hide("#verse-picker-anchor");
  setTimeout(function() {
    Tipped.show("#verse-picker-anchor");
    $("#tipped-expander").height(calculatedExpanderHeight());
  }, 0);
}
