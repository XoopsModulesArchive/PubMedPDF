<?php

/*
 Require PHP5, uses built-in DOM extension.
 To be used in PHP4 scripts using DOMXML extension.
 Allows PHP4/DOMXML scripts to run on PHP5/DOM.
 (Requires PHP5/XSL extension for domxml_xslt functions)

 Typical use:
 {
  if (version_compare(PHP_VERSION,'5','>='))
   require_once __DIR__ . '/domxml-php4-to-php5.php';
 }

 Version 1.7.2, 2005-09-08, http://alexandre.alapetite.net/doc-alex/domxml-php4-php5/

 ------------------------------------------------------------------
 Written by Alexandre Alapetite, http://alexandre.alapetite.net/cv/

 Copyright 2004-2005, Licence: Creative Commons "Attribution-ShareAlike 2.0 France" BY-SA (FR),
 http://creativecommons.org/licenses/by-sa/2.0/fr/
 http://alexandre.alapetite.net/divers/apropos/#by-sa
 - Attribution. You must give the original author credit
 - Share Alike. If you alter, transform, or build upon this work,
   you may distribute the resulting work only under a license identical to this one
 - The French law is authoritative
 - Any of these conditions can be waived if you get permission from Alexandre Alapetite
 - Please send to Alexandre Alapetite the modifications you make,
   in order to improve this file for the benefit of everybody

 If you want to distribute this code, please do it as a link to:
 http://alexandre.alapetite.net/doc-alex/domxml-php4-php5/
*/

function domxml_new_doc($version)
{
    return new php4DOMDocument('');
}

function domxml_open_file($filename)
{
    return new php4DOMDocument($filename);
}

function domxml_open_mem($str)
{
    $dom = new php4DOMDocument('');

    $dom->myDOMNode->loadXML($str);

    return $dom;
}

function xpath_eval($xpath_context, $eval_str, $contextnode = null)
{
    return $xpath_context->xpath_eval($eval_str, $contextnode);
}

function xpath_new_context($dom_document)
{
    return new php4DOMXPath($dom_document);
}

function xpath_register_ns($xpath_context, $prefix, $namespaceURI)
{
    return $xpath_context->myDOMXPath->registerNamespace($prefix, $namespaceURI);
}

class php4DOMAttr extends php4DOMNode
{
    public function __construct($aDOMAttr)
    {
        $this->myDOMNode = $aDOMAttr;
    }

    public function __get($name)
    {
        if ('name' == $name) {
            return $this->myDOMNode->name;
        } elseif ('value' == $name) {
            return $this->myDOMNode->value;
        }

        return parent::__get($name);
    }

    public function name()
    {
        return $this->myDOMNode->name;
    }

    public function set_value($content)
    {
        return $this->myDOMNode->value = $content;
    }

    public function specified()
    {
        return $this->myDOMNode->specified;
    }

    public function value()
    {
        return $this->myDOMNode->value;
    }
}

class php4DOMDocument extends php4DOMNode
{
    public function __construct($filename = '')
    {
        $this->myDOMNode = new DOMDocument();

        $this->myOwnerDocument = $this;

        if ('' != $filename) {
            $this->myDOMNode->load($filename);
        }
    }

    public function add_root($name)
    {
        if ($this->myDOMNode->hasChildNodes()) {
            $this->myDOMNode->removeChild($this->myDOMNode->firstChild);
        }

        return new php4DOMElement($this->myDOMNode->appendChild($this->myDOMNode->createElement($name)), $this->myOwnerDocument);
    }

    public function create_attribute($name, $value)
    {
        $myAttr = $this->myDOMNode->createAttribute($name);

        $myAttr->value = $value;

        return new php4DOMAttr($myAttr, $this);
    }

    public function create_cdata_section($content)
    {
        return new php4DOMNode($this->myDOMNode->createCDATASection($content), $this);
    }

    public function create_comment($data)
    {
        return new php4DOMNode($this->myDOMNode->createComment($data), $this);
    }

    public function create_element($name)
    {
        return new php4DOMElement($this->myDOMNode->createElement($name), $this);
    }

    public function create_text_node($content)
    {
        return new php4DOMNode($this->myDOMNode->createTextNode($content), $this);
    }

    public function document_element()
    {
        return $this->_newDOMElement($this->myDOMNode->documentElement, $this);
    }

    public function dump_file($filename, $compressionmode = false, $format = false)
    {
        $format0 = $this->myDOMNode->formatOutput;

        $this->myDOMNode->formatOutput = $format;

        $res = $this->myDOMNode->save($filename);

        $this->myDOMNode->formatOutput = $format0;

        return $res;
    }

    public function dump_mem($format = false, $encoding = false)
    {
        $format0 = $this->myDOMNode->formatOutput;

        $this->myDOMNode->formatOutput = $format;

        $encoding0 = $this->myDOMNode->encoding;

        if ($encoding) {
            $this->myDOMNode->encoding = $encoding;
        }

        $dump = $this->myDOMNode->saveXML();

        $this->myDOMNode->formatOutput = $format0;

        if ($encoding) {
            $this->myDOMNode->encoding = '' == $encoding0 ? 'UTF-8' : $encoding0;
        } //UTF-8 is XML default encoding

        return $dump;
    }

    public function dump_node($node)
    {
        return $this->myDOMNode->saveXML($node->myDOMNode);
    }

    public function free()
    {
        if ($this->myDOMNode->hasChildNodes()) {
            $this->myDOMNode->removeChild($this->myDOMNode->firstChild);
        }

        $this->myDOMNode = null;

        $this->myOwnerDocument = null;
    }

    public function get_element_by_id($id)
    {
        return $this->_newDOMElement($this->myDOMNode->getElementById($id), $this);
    }

    public function get_elements_by_tagname($name)
    {
        $myDOMNodeList = $this->myDOMNode->getElementsByTagName($name);

        $nodeSet = [];

        $i = 0;

        if (isset($myDOMNodeList)) {
            while ($node = $myDOMNodeList->item($i)) {
                $nodeSet[] = new php4DOMElement($node, $this);

                $i++;
            }
        }

        return $nodeSet;
    }

    public function html_dump_mem()
    {
        return $this->myDOMNode->saveHTML();
    }

    public function root()
    {
        return $this->_newDOMElement($this->myDOMNode->documentElement, $this);
    }

    public function xpath_new_context()
    {
        return new php4DOMXPath($this);
    }
}

class php4DOMElement extends php4DOMNode
{
    public function get_attribute($name)
    {
        return $this->myDOMNode->getAttribute($name);
    }

    public function get_elements_by_tagname($name)
    {
        $myDOMNodeList = $this->myDOMNode->getElementsByTagName($name);

        $nodeSet = [];

        $i = 0;

        if (isset($myDOMNodeList)) {
            while ($node = $myDOMNodeList->item($i)) {
                $nodeSet[] = new self($node, $this->myOwnerDocument);

                $i++;
            }
        }

        return $nodeSet;
    }

    public function has_attribute($name)
    {
        return $this->myDOMNode->hasAttribute($name);
    }

    public function remove_attribute($name)
    {
        return $this->myDOMNode->removeAttribute($name);
    }

    public function set_attribute($name, $value)
    {
        return $this->myDOMNode->setAttribute($name, $value);
    }

    public function tagname()
    {
        return $this->myDOMNode->tagName;
    }
}

class php4DOMNode
{
    public $myDOMNode;

    public $myOwnerDocument;

    public function __construct($aDomNode, $aOwnerDocument)
    {
        $this->myDOMNode = $aDomNode;

        $this->myOwnerDocument = $aOwnerDocument;
    }

    public function __get($name)
    {
        if ('type' == $name) {
            return $this->myDOMNode->nodeType;
        } elseif ('tagname' == $name) {
            return $this->myDOMNode->tagName;
        } elseif ('content' == $name) {
            return $this->myDOMNode->textContent;
        }

        $myErrors = debug_backtrace();

        trigger_error('Undefined property: ' . get_class($this) . '::$' . $name . ' [' . $myErrors[0]['file'] . ':' . $myErrors[0]['line'] . ']', E_USER_NOTICE);

        return false;
    }

    public function append_child($newnode)
    {
        return new php4DOMElement($this->myDOMNode->appendChild($this->_importNode($newnode)), $this->myOwnerDocument);
    }

    public function append_sibling($newnode)
    {
        return new php4DOMElement($this->myDOMNode->parentNode->appendChild($this->_importNode($newnode)), $this->myOwnerDocument);
    }

    public function attributes()
    {
        $myDOMNodeList = $this->myDOMNode->attributes;

        $nodeSet = [];

        $i = 0;

        if (isset($myDOMNodeList)) {
            while ($node = $myDOMNodeList->item($i)) {
                $nodeSet[] = new php4DOMAttr($node, $this->myOwnerDocument);

                $i++;
            }
        }

        return $nodeSet;
    }

    public function child_nodes()
    {
        $myDOMNodeList = $this->myDOMNode->childNodes;

        $nodeSet = [];

        $i = 0;

        if (isset($myDOMNodeList)) {
            while ($node = $myDOMNodeList->item($i)) {
                $nodeSet[] = new php4DOMElement($node, $this->myOwnerDocument);

                $i++;
            }
        }

        return $nodeSet;
    }

    public function children()
    {
        return $this->child_nodes();
    }

    public function clone_node($deep = false)
    {
        return new php4DOMElement($this->myDOMNode->cloneNode($deep), $this->myOwnerDocument);
    }

    public function dump_node()
    {
        return $this->myOwnerDocument->myDOMNode->saveXML($this->myDOMNode);
    }

    public function first_child()
    {
        return $this->_newDOMElement($this->myDOMNode->firstChild, $this->myOwnerDocument);
    }

    public function get_content()
    {
        return $this->myDOMNode->textContent;
    }

    public function has_attributes()
    {
        return $this->myDOMNode->hasAttributes();
    }

    public function has_child_nodes()
    {
        return $this->myDOMNode->hasChildNodes();
    }

    public function insert_before($newnode, $refnode)
    {
        return new php4DOMElement($this->myDOMNode->insertBefore($newnode->myDOMNode, $refnode->myDOMNode), $this->myOwnerDocument);
    }

    public function is_blank_node()
    {
        return (XML_TEXT_NODE == $this->myDOMNode->nodeType) && preg_match('^([[:cntrl:]]|[[:space:]])*$', $this->myDOMNode->nodeValue);
    }

    public function last_child()
    {
        return $this->_newDOMElement($this->myDOMNode->lastChild, $this->myOwnerDocument);
    }

    public function new_child($name, $content)
    {
        $mySubNode = $this->myDOMNode->ownerDocument->createElement($name);

        $mySubNode->appendChild($this->myDOMNode->ownerDocument->createTextNode(html_entity_decode($content, ENT_QUOTES)));

        $this->myDOMNode->appendChild($mySubNode);

        return new php4DOMElement($mySubNode, $this->myOwnerDocument);
    }

    public function next_sibling()
    {
        return $this->_newDOMElement($this->myDOMNode->nextSibling, $this->myOwnerDocument);
    }

    public function node_name()
    {
        if (XML_ELEMENT_NODE == $this->myDOMNode->nodeType) {
            return $this->myDOMNode->localName;
        } //avoid namespace prefix

        return $this->myDOMNode->nodeName;
    }

    public function node_type()
    {
        return $this->myDOMNode->nodeType;
    }

    public function node_value()
    {
        return $this->myDOMNode->nodeValue;
    }

    public function owner_document()
    {
        return $this->myOwnerDocument;
    }

    public function parent_node()
    {
        return $this->_newDOMElement($this->myDOMNode->parentNode, $this->myOwnerDocument);
    }

    public function prefix()
    {
        return $this->myDOMNode->prefix;
    }

    public function previous_sibling()
    {
        return $this->_newDOMElement($this->myDOMNode->previousSibling, $this->myOwnerDocument);
    }

    public function remove_child($oldchild)
    {
        return $this->_newDOMElement($this->myDOMNode->removeChild($oldchild->myDOMNode), $this->myOwnerDocument);
    }

    public function replace_child($oldnode, $newnode)
    {
        return $this->_newDOMElement($this->myDOMNode->replaceChild($oldnode->myDOMNode, $this->_importNode($newnode)), $this->myOwnerDocument);
    }

    public function set_content($text)
    {
        return $this->myDOMNode->appendChild($this->myDOMNode->ownerDocument->createTextNode($text));
    }

    public function _importNode($newnode)
    {//Private function to import DOMNode from another DOMDocument
        if ($this->myOwnerDocument === $newnode->myOwnerDocument) {
            return $newnode->myDOMNode;
        }

        return $this->myOwnerDocument->myDOMNode->importNode($newnode->myDOMNode, true);
    }

    public function _newDOMElement($aDOMNode, $aOwnerDocument)
    {//Private function to check the PHP5 DOMNode before creating a new associated PHP4 DOMNode wrapper
        if (null === $aDOMNode) {
            return null;
        } elseif (XML_ELEMENT_NODE == $aDOMNode->nodeType) {
            return new php4DOMElement($aDOMNode, $aOwnerDocument);
        } elseif (XML_ATTRIBUTE_NODE == $aDOMNode->nodeType) {
            return new php4DOMAttr($aDOMNode, $aOwnerDocument);
        }

        return new self($aDOMNode, $aOwnerDocument);
    }
}

class php4DOMNodelist
{
    public $myDOMNodelist;

    public $nodeset;

    public function __construct($aDOMNodelist, $aOwnerDocument)
    {
        $this->myDOMNodelist = $aDOMNodelist;

        $this->nodeset = [];

        $i = 0;

        if (isset($this->myDOMNodelist)) {
            while ($node = $this->myDOMNodelist->item($i)) {
                switch ($node->nodeType) {
                    case XML_ATTRIBUTE_NODE:
                        $this->nodeset[] = new php4DOMAttr($node, $aOwnerDocument);
                        break;
                    case XML_ELEMENT_NODE:
                    default:
                        $this->nodeset[] = new php4DOMElement($node, $aOwnerDocument);
                }

                $i++;
            }
        }
    }
}

class php4DOMXPath
{
    public $myDOMXPath;

    public $myOwnerDocument;

    public function __construct($dom_document)
    {
        $this->myOwnerDocument = $dom_document;

        $this->myDOMXPath = new DOMXPath($dom_document->myDOMNode);
    }

    public function xpath_eval($eval_str, $contextnode = null)
    {
        if (isset($contextnode)) {
            return new php4DOMNodelist($this->myDOMXPath->query($eval_str, $contextnode->myDOMNode), $this->myOwnerDocument);
        }

        return new php4DOMNodelist($this->myDOMXPath->query($eval_str), $this->myOwnerDocument);
    }

    public function xpath_register_ns($prefix, $namespaceURI)
    {
        return $this->myDOMXPath->registerNamespace($prefix, $namespaceURI);
    }
}

if (extension_loaded('xsl')) {//See also: http://alexandre.alapetite.net/doc-alex/xslt-php4-php5/
    function domxml_xslt_stylesheet($xslstring)
    {
        return new php4DomXsltStylesheet(DOMDocument::loadXML($xslstring));
    }

    function domxml_xslt_stylesheet_doc($dom_document)
    {
        return new php4DomXsltStylesheet($dom_document);
    }

    function domxml_xslt_stylesheet_file($xslfile)
    {
        return new php4DomXsltStylesheet(DOMDocument::load($xslfile));
    }

    class php4DomXsltStylesheet
    {
        public $myxsltProcessor;

        public function __construct($dom_document)
        {
            $this->myxsltProcessor = new XSLTProcessor();

            $this->myxsltProcessor->importStylesheet($dom_document);
        }

        public function process($dom_document, $xslt_parameters = [], $param_is_xpath = false)
        {
            foreach ($xslt_parameters as $param => $value) {
                $this->myxsltProcessor->setParameter('', $param, $value);
            }

            $myphp4DOMDocument = new php4DOMDocument();

            $myphp4DOMDocument->myDOMNode = $this->myxsltProcessor->transformToDoc($dom_document->myDOMNode);

            return $myphp4DOMDocument;
        }

        public function result_dump_file($dom_document, $filename)
        {
            $html = $dom_document->myDOMNode->saveHTML();

            file_put_contents($filename, $html);

            return $html;
        }

        public function result_dump_mem($dom_document)
        {
            return $dom_document->myDOMNode->saveHTML();
        }
    }
}
