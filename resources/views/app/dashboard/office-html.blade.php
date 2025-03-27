@extends('layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<!-- jQuery (Load First) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

<!--PDF-->
<link rel="stylesheet" href="{{ asset('include/pdf/pdf.viewer.css') }}">
<script src="{{ asset('include/pdf/pdf.js') }}"></script>

<!--Docs-->
<script src="{{ asset('include/docx/jszip-utils.js') }}"></script>
<script src="{{ asset('include/docx/mammoth.browser.min.js') }}"></script>

<!--PPTX-->
<link rel="stylesheet" href="{{ asset('include/PPTXjs/css/pptxjs.css') }}">
<link rel="stylesheet" href="{{ asset('include/PPTXjs/css/nv.d3.min.css') }}">
<link rel="stylesheet" href="{{ asset('include/revealjs/reveal.css') }}">

<script type="text/javascript" src="{{ asset('include/PPTXjs/js/filereader.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/nv.d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/pptxjs.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/divs2slides.js') }}"></script>

<!--All Spreadsheet -->
<link rel="stylesheet" href="{{ asset('include/SheetJS/handsontable.full.min.css') }}">
<script type="text/javascript" src="{{ asset('include/SheetJS/handsontable.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/SheetJS/xlsx.full.min.js') }}"></script>

<!--Image viewer-->
<link rel="stylesheet" href="{{ asset('include/verySimpleImageViewer/css/jquery.verySimpleImageViewer.css') }}">
<script type="text/javascript" src="{{ asset('include/verySimpleImageViewer/js/jquery.verySimpleImageViewer.js') }}"></script>

<!-- officeToHtml -->
<script src="{{ asset('js/officeToHtml.js') }}?v={{ time() }}"></script>
<link rel="stylesheet" href="{{ asset('css/officeToHtml.css') }}">

<body id="top">
  <div class="wrapper row1">
    <header id="header" class="hoc clear">
      <div id="logo" class="fl_left">
        <h1><a href="../index.html">Office To Html - Demos</a></h1>
      </div>
      <nav id="mainav" class="fl_right">
        <ul class="clear">
          <li><a href="../index.html">Home</a></li>
          <li><a href="docs.html">Docs</a></li>
          <li class="active"><a href="demos.html">Demos</a></li>
          <li><a href="https://github.com/meshesha/officeToHtml">GitHub</a></li>
          <li><a href="https://github.com/meshesha">About</a></li>
        </ul>
      </nav>
    </header>
  </div>
  <div class="wrapper row3">
    <main class="hoc container clear">
      <!-- main body -->
      <div class="sidebar one_quarter first">
        <nav class="sdb_holder">
          <ul>
            <li class="active"><a href="demos.html">Demos - Main</a></li>
            <ul>
              <li><a href="#" id="demo_1" class="demos" data-file="demo.docx">Demo1 - docx</a></li>
              <li><a href="#" id="demo_2" class="demos" data-file="demo.pptx">Demo2 - pptx</a></li>
              <li><a href="#" id="demo_3" class="demos" data-file="demo.xlsx">Demo3 - xlsx</a></li>
              <li><a href="#" id="demo_4" class="demos" data-file="demo.pdf">Demo4 - pdf</a></li>
              <li><a href="#" id="demo_5" class="demos" data-file="demo.jpg">Demo4 - image</a></li>
              <li><a href="#" id="demo_input" class="demos" data-file="">Demo5 - Input</a></li>
            </ul>
          </ul>
        </nav>
      </div>
      <!-- ################################################################################################ -->
      <div class="content three_quarter">
        <div class="box bg-light clear">
          <h3 class="font-x2" id="head-name">Demos - Main</h3>
          <p id="file_p" style="display:none;">File: <a href="#" id="a_file"></a><input type="file" id="select_file" />
          </p>
          <div id="description">
            <p>&lt;-- Select one of demos on left side</p>
          </div>
        </div>
        <p id="resolte-text" style="display:none">Resolte:</p>
        <!--<div id="resolte-contaniner" style="display:none;"></div>-->
        <div style="overflow: hidden;width: 800px; ">
          <div id="resolte-contaniner" style="width: 100%; height:550px; overflow: auto;"></div>
        </div>
        <iframe src="http://docs.google.com/gview?url=public/files/demo.pptx&embedded=true" style="width:550px; height:450px;" frameborder="0"></iframe>

        <script>
          (function ($) {
            $(".demos").on("click", function (e) {
              e.preventDefault();

              $(".sdb_holder li").removeClass("active");
              $(this).parent().addClass("active");
              var id = $(this).attr("id");
              $("#head-name").html($(this).html());
              $("#description").hide();
              $("#resolte-contaniner").html("");
              $("#resolte-contaniner").show();
              $("#resolte-text").show();
              if (id != "demo_input") {

                $("#select_file").hide();
                var file_path = "files\\" + $(this).data("file");
                $("#a_file").html($(this).data("file")).attr("href", file_path);
                $("#a_file").show();
                $("#file_p").show();

                $("#resolte-contaniner").officeToHtml({
                  url: file_path,
                  pdfSetting: {
                    setLang: "",
                    setLangFilesPath: "" /*"include/pdf/lang/locale" - relative to app path*/
                  }
                });
              } else {

                $("#select_file").show();
                $("#file_p").show();
                $("#a_file").hide();

                $("#resolte-contaniner").officeToHtml({
                  inputObjId: "select_file",
                  pdfSetting: {
                    setLang: "",
                    setLangFilesPath: "" /*"include/pdf/lang/locale" - relative to app path*/
                  }
                });
              }
            });
          }(jQuery));
        </script>
      </div>
  </div>
  <!-- / main body -->
  <div class="clear"></div>
  </main>
  </div>

  <a id="backtotop" href="#top"><i class="fa fa-chevron-up"></i></a>
</body>

@endsection