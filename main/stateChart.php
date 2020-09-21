﻿<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}


// Include config file
require_once "../php/config.php";
 
?>

<!--------------------------php--------------------------------->

<!DOCTYPE html>
<html>
  <head>
    <title>State Chart</title>
    <!-- Copyright 1998-2020 by Northwoods Software Corporation. -->
    <meta
      name="description"
      content="gojs"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css"
      integrity="sha384-VCmXjywReHh4PwowAiWNagnWcLhlEJLA5buUprzK8rxFgeH0kww/aWY76TfkUoSX"
      crossorigin="anonymous"
    />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="js/capture.js"></script>
    <script src="../release/go.js"></script>
   




    <script id="code">
      function init() {
        if (window.goSamples) goSamples(); // init for these samples -- you don't need to call this
        var $ = go.GraphObject.make; // for conciseness in defining templates

        // some constants that will be reused within templates
        var roundedRectangleParams = {
          parameter1: 2, // set the rounded corner
          spot1: go.Spot.TopLeft,
          spot2: go.Spot.BottomRight, // make content go all the way to inside edges of rounded corners
        };

        myDiagram = $(
          go.Diagram,
          "myDiagramDiv", // must name or refer to the DIV HTML element
          {
            "animationManager.initialAnimationStyle": go.AnimationManager.None,
            InitialAnimationStarting: function (e) {
              var animation = e.subject.defaultAnimation;
              animation.easing = go.Animation.EaseOutExpo;
              animation.duration = 900;
              animation.add(e.diagram, "scale", 0.1, 1);
              animation.add(e.diagram, "opacity", 0, 1);
            },

            // have mouse wheel events zoom in and out instead of scroll up and down
            "toolManager.mouseWheelBehavior": go.ToolManager.WheelZoom,
            // support double-click in background creating a new node
            "clickCreatingTool.archetypeNodeData": { text: "new node" },
            // enable undo & redo
            "undoManager.isEnabled": true,
            positionComputation: function (diagram, pt) {
              return new go.Point(Math.floor(pt.x), Math.floor(pt.y));
            },
          }
        );

        // when the document is modified, add a "*" to the title and enable the "Save" button
        myDiagram.addDiagramListener("Modified", function (e) {
          var button = document.getElementById("SaveButton");
          if (button) button.disabled = !myDiagram.isModified;
          var idx = document.title.indexOf("*");
          if (myDiagram.isModified) {
            if (idx < 0) document.title += "*";
          } else {
            if (idx >= 0) document.title = document.title.substr(0, idx);
          }
        });

        // define the Node template
        myDiagram.nodeTemplate = $(
          go.Node,
          "Auto",
          {
            locationSpot: go.Spot.Top,
            isShadowed: true,
            shadowBlur: 1,
            shadowOffset: new go.Point(0, 1),
            shadowColor: "rgba(0, 0, 0, .14)",
          },
          new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
            go.Point.stringify
          ),
          // define the node's outer shape, which will surround the TextBlock
          $(go.Shape, "RoundedRectangle", roundedRectangleParams, {
            name: "SHAPE",
            fill: "#ffffff",
            strokeWidth: 0,
            stroke: null,
            portId: "", // this Shape is the Node's port, not the whole Node
            fromLinkable: true,
            fromLinkableSelfNode: true,
            fromLinkableDuplicates: true,
            toLinkable: true,
            toLinkableSelfNode: true,
            toLinkableDuplicates: true,
            cursor: "pointer",
          }),
          $(
            go.TextBlock,
            {
              font: "bold small-caps 11pt helvetica, bold arial, sans-serif",
              margin: 7,
              stroke: "rgba(0, 0, 0, .87)",
              editable: true, // editing the text automatically updates the model data
            },
            new go.Binding("text").makeTwoWay()
          )
        );

        // unlike the normal selection Adornment, this one includes a Button
        myDiagram.nodeTemplate.selectionAdornmentTemplate = $(
          go.Adornment,
          "Spot",
          $(
            go.Panel,
            "Auto",
            $(go.Shape, "RoundedRectangle", roundedRectangleParams, {
              fill: null,
              stroke: "#7986cb",
              strokeWidth: 3,
            }),
            $(go.Placeholder) // a Placeholder sizes itself to the selected Node
          ),
          // the button to create a "next" node, at the top-right corner
          $(
            "Button",
            {
              alignment: go.Spot.TopRight,
              click: addNodeAndLink, // this function is defined below
            },
            $(go.Shape, "PlusLine", { width: 6, height: 6 })
          ) // end button
        ); // end Adornment

        myDiagram.nodeTemplateMap.add(
          "Start",
          $(
            go.Node,
            "Spot",
            { desiredSize: new go.Size(75, 75) },
            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
              go.Point.stringify
            ),
            $(go.Shape, "Circle", {
              fill: "#52ce60" /* green */,
              stroke: null,
              portId: "",
              fromLinkable: true,
              fromLinkableSelfNode: true,
              fromLinkableDuplicates: true,
              toLinkable: true,
              toLinkableSelfNode: true,
              toLinkableDuplicates: true,
              cursor: "pointer",
            }),
            $(go.TextBlock, "Start", {
              font: "bold 16pt helvetica, bold arial, sans-serif",
              stroke: "whitesmoke",
            })
          )
        );

        myDiagram.nodeTemplateMap.add(
          "End",
          $(
            go.Node,
            "Spot",
            { desiredSize: new go.Size(75, 75) },
            new go.Binding("location", "loc", go.Point.parse).makeTwoWay(
              go.Point.stringify
            ),
            $(go.Shape, "Circle", {
              fill: "maroon",
              stroke: null,
              portId: "",
              fromLinkable: true,
              fromLinkableSelfNode: true,
              fromLinkableDuplicates: true,
              toLinkable: true,
              toLinkableSelfNode: true,
              toLinkableDuplicates: true,
              cursor: "pointer",
            }),
            $(go.Shape, "Circle", {
              fill: null,
              desiredSize: new go.Size(65, 65),
              strokeWidth: 2,
              stroke: "whitesmoke",
            }),
            $(go.TextBlock, "End", {
              font: "bold 16pt helvetica, bold arial, sans-serif",
              stroke: "whitesmoke",
            })
          )
        );

        // clicking the button inserts a new node to the right of the selected node,
        // and adds a link to that new node
        function addNodeAndLink(e, obj) {
          var adornment = obj.part;
          var diagram = e.diagram;
          diagram.startTransaction("Add State");

          // get the node data for which the user clicked the button
          var fromNode = adornment.adornedPart;
          var fromData = fromNode.data;
          // create a new "State" data object, positioned off to the right of the adorned Node
          var toData = { text: "new" };
          var p = fromNode.location.copy();
          p.x += 200;
          toData.loc = go.Point.stringify(p); // the "loc" property is a string, not a Point object
          // add the new node data to the model
          var model = diagram.model;
          model.addNodeData(toData);

          // create a link data from the old node data to the new node data
          var linkdata = {
            from: model.getKeyForNodeData(fromData), // or just: fromData.id
            to: model.getKeyForNodeData(toData),
            text: "transition",
          };
          // and add the link data to the model
          model.addLinkData(linkdata);

          // select the new Node
          var newnode = diagram.findNodeForData(toData);
          diagram.select(newnode);

          diagram.commitTransaction("Add State");

          // if the new node is off-screen, scroll the diagram to show the new node
          diagram.scrollToRect(newnode.actualBounds);
        }

        // replace the default Link template in the linkTemplateMap
        myDiagram.linkTemplate = $(
          go.Link, // the whole link panel
          {
            curve: go.Link.Bezier,
            adjusting: go.Link.Stretch,
            reshapable: true,
            relinkableFrom: true,
            relinkableTo: true,
            toShortLength: 3,
          },
          {
            click: function (e, link) {
              e.diagram.commit(function (diag) {
                link.path.stroke = "royalblue";
              });
            },
            doubleClick: function (e, link) {
              e.diagram.commit(function (diag) {
                link.path.stroke = "red";
              });
            },
            mouseHover: function (e, link) {
              e.diagram.commit(function (diag) {
                link.path.stroke = "green";
              });
            },
          },
          new go.Binding("points").makeTwoWay(),
          new go.Binding("curviness"),
          $(
            go.Shape, // the link shape
            { strokeWidth: 1.5 },
            new go.Binding("stroke", "progress", function (progress) {
              return progress ? "#52ce60" /* green */ : "black";
            }),
            new go.Binding("strokeWidth", "progress", function (progress) {
              return progress ? 2.5 : 1.5;
            })
          ),
          $(
            go.Shape, // the arrowhead
            { toArrow: "standard", stroke: null },
            new go.Binding("fill", "progress", function (progress) {
              return progress ? "#52ce60" /* green */ : "black";
            })
          ),
          $(
            go.Panel,
            "Auto",
            $(
              go.Shape, // the label background, which becomes transparent around the edges
              {
                fill: $(go.Brush, "Radial", {
                  0: "rgb(245, 245, 245)",
                  0.7: "rgb(245, 245, 245)",
                  1: "rgba(245, 245, 245, 0)",
                }),
                stroke: null,
              }
            ),
            $(
              go.TextBlock,
              "transition", // the label text
              {
                textAlign: "center",
                font: "9pt helvetica, arial, sans-serif",
                margin: 4,
                editable: true, // enable in-place editing
              },
              // editing the text automatically updates the model data
              new go.Binding("text").makeTwoWay()
            )
          )
        );

        // read in the JSON data from the "mySavedModel" element
        load();
       
      }

      // Show the diagram's model in JSON format
      function save() {
        document.getElementById(
          "mySavedModel"
        ).value = myDiagram.model.toJson();
      }
      function load() {
        myDiagram.model = go.Model.fromJson(
          document.getElementById("mySavedModel").value
        );
      }
    </script>
     <!-- Script -->


  <script>
  document.querySelector('button').addEventListener('click', function() {
        html2canvas(document.querySelector('.specific'), {
            onrendered: function(canvas) {
                // document.body.appendChild(canvas);
              return Canvas2Image.saveAsPNG(canvas);
            }
        });
    });
  </script>

  </head>



  <!-- Just an image -->
<div class="container">
<nav class="navbar navbar-light mt-4" style="background-color: #e3f2fd;">
  <div class="col-md-6">
  <a class="navbar-brand "  href="#">
    <img src="../php/images/s.png" width="40" height="40" alt="">  
  </a>
  </div>
  <div class="col-md-3">
  <h4 class="text-right">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></h4>
  </div> 
  <div class="col-md-3">
  <a href="../php/  logout.php" class="btn btn-danger text-right">Sign Out</a>
  </div>
</nav>
</div>
 
  <body onload="init()">
    <div class="container mt-4">
      <div id="sample">
        <div
          id="myDiagramDiv"
          style="
            border: solid 1px black;
            width: 100%;
            height: 700px;
            background: whitesmoke;"></div>

        <div>
          <div>
            <button id="btn">Capture</button>
            
            <button id="SaveButton" onclick="save()">Save</button>
            <button onclick="load()">Load</button>
          Diagram Model saved in JSON format:
          </div>
          <textarea id="mySavedModel"> 
          </textarea >
          
          <!--script--->
          <script>
      const capture = () => {
        const body = document.querySelector("body");
        body.id = "capture";
        html2canvas(document.querySelector("#capture"))
          .then((canvas) => {
            document.body.appendChild(canvas);
          })
          .then(() => {
            var canvas = document.querySelector("canvas");
            canvas.style.display = "none";
            var image = canvas
              .toDataURL("image/png")
              .replace("image/png", "image/octet-stream");
            var a = document.createElement("a");
            a.setAttribute("download", "myImage.png");
            a.setAttribute("href", image);
            a.click();
          });
      };

      const btn = document.getElementById("btn");
      btn.addEventListener("click", capture);
    </script>
        <!--script--->

          
        </div>
      </div>
    </div>
  </body>
</html>
