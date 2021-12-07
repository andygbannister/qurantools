<?php

// display the PDF page we want

$FOLDER = $_POST["FOLDER"];
$PREFIX = $_POST["PREFIX"];
$PAGE   = $_POST["PAGE"];

// navigator

if (substr($FOLDER, -7) == "Jeffery")
{
    $resourceTitle              = "The Foreign Vocabulary of the Qur&rsquo;an</i> (Arthur Jeffery)";
    $resourceLastFileNumberPage = 325;            // this is the large PDF number (which will be higher than below, due to prologues etc)
    $resourceLastNumberedPage   = 311;    // this is the last "page number" in the resource
    $RESOURCE_CODE              = "JEFFERY";
}

if (substr($FOLDER, -7) == "Penrice")
{
    $resourceTitle              = "A Dictionary and Glossary of the Qur&rsquo;an</i> (John Penrice)";
    $resourceLastFileNumberPage = 176;            // this is the large PDF number (which will be higher than below, due to prologues etc)
    $resourceLastNumberedPage   = 167;    // this is the last "page number" in the resource
    $RESOURCE_CODE              = "PENRICE";
}

if (substr($FOLDER, -4) == "Lane")
{
    $resourceTitle              = "An Arabic-English Lexicon</i> (Edward William Lane)";
    $resourceLastFileNumberPage = 3099;
    if (isset($_POST["VOLUME"]))
    {
        $FOLDER .= "/vol" . $_POST["VOLUME"];
    } // Lane has volumes, so we need to account for this
    $resourceLastNumberedPage = 3064;
    $RESOURCE_CODE            = "LANE";
}

// echo "<span style='float:right; margin-right:20px;' title='Open this PDF page in a new window'><a href='../dictionary/pdf_simple_view.php?FOLDER=$FOLDER&PREFIX=$PREFIX&PAGE=$PAGE' target='_blank'><img src='../images/expand.png'></a></span>";

echo "<span align=center><h2 class='page-title-text'><i>$resourceTitle</i></h2></span>";

echo "<div style='margin-top:4px; margin-bottom:12px'>";

echo "<img src='../images/arrow-left-terminal.gif' width=14 height=12 onclick=\"GotoPage(1);\">&nbsp;&nbsp;&nbsp;";
echo "<img src='../images/arrow-left-double.gif' width=19 height=12 onclick=\"GotoPage(-" . ($PAGE - 10) . ");\">&nbsp;&nbsp;&nbsp;";
echo "<img src='../images/arrow-left-single.gif' width=11 height=12 onclick=\"GotoPage(" . ($PAGE - 1) . ");\">&nbsp;&nbsp;&nbsp;";

// different resources need a different offset, due to prologues, odd numbering, etc.

if ($RESOURCE_CODE == "PENRICE")
{
    echo "<select NAME=PAGEJUMP ID=PAGEJUMP onChange=\"GotoPage(parseInt(this.value) + 9);\">";
}

if ($RESOURCE_CODE == "LANE")
{
    echo "<select NAME=PAGEJUMP ID=PAGEJUMP onChange=\"GotoPage(parseInt(this.value) + 36);\">";
}

if ($RESOURCE_CODE == "JEFFERY")
{
    echo "<select NAME=PAGEJUMP ID=PAGEJUMP onChange=\"GotoPage(parseInt(this.value) + 14);\">";
}

echo "<option value=0>Jump to page ...</option>";

for ($i = 1; $i <= $resourceLastNumberedPage; $i++)
{
    // there is no page 369 in Lane Volume 1
    if ($i == 369 && $FOLDER == "Lane/vol1")
    {
        // do nothing
    }
    else
    {
        if ($i >= 370 && $FOLDER == "Lane/vol1")
        {
            echo "<option value=" . ($i - 1) . ">$i</option>";
        }
        else
        {
            echo "<option value=$i>$i</option>";
        }
    }
}

echo "</select>&nbsp;&nbsp;&nbsp;";

// echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

echo "<img src='../images/arrow-right-single.gif' width=19 height=12 onclick=\"GotoPage(" . ($PAGE + 1) . ");\">&nbsp;&nbsp;&nbsp;";
echo "<img src='../images/arrow-right-double.gif' width=19 height=12 onclick=\"GotoPage(" . ($PAGE + 10) . ");\">&nbsp;&nbsp;&nbsp;";
echo "<img src='../images/arrow-right-terminal.gif' width=14 height=12 onclick=\"GotoPage($resourceLastFileNumberPage);\">";
echo "</div>";

echo "<iframe width=900 height=1200 src='$FOLDER/$PREFIX$PAGE.pdf'></iframe>";
