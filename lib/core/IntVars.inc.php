<?
## $Id$

## This file is part of the CERN Document Server Software (CDSware).
## Copyright (C) 2002, 2003, 2004, 2005 CERN.
##
## The CDSware is free software; you can redistribute it and/or
## modify it under the terms of the GNU General Public License as
## published by the Free Software Foundation; either version 2 of the
## License, or (at your option) any later version.
##
## The CDSware is distributed in the hope that it will be useful, but
## WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## General Public License for more details.  
##
## You should have received a copy of the GNU General Public License
## along with CDSware; if not, write to the Free Software Foundation, Inc.,
## 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

//==========================================================================
//  File: IntVars.inc (flexElink core)
//  Classes: IntVarValue
//  Requires: 
//  Included:   
//==========================================================================


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Class: IntVarValue
//  Purpose:
//  Attributes:
//  Methods:
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class IntVarValue {
  var $value; //string
  var $sfvalues; //array that will contain subfield values for this value; 
		 //  key will indicate the sfname, and value will be the string
		 //   value itself
		 //If a subfield hasn't existed for this value, it won't 
		 //   appear as a key in the array

  function IntVarValue($value, $sfvalues=null)
  {
    $this->value=$value;
    $this->sfvalues=array();
    $this->addSFValues($sfvalues);
  }

  function hasSF( $sfname )
  {
    $sfname=strtoupper(trim($sfname));
    //return in_array($sfname, array_keys($this->sfvalues));
    return isset($this->sfvalues[$sfname]);
  }

  function addValue( $value )
  {
    //$this->value.=$value;
    $this->value=$value;
  }

  function addSFValues( $sfvalues )
  {
    if($sfvalues)
    {
      foreach($sfvalues as $key=>$val)
      {
	$this->addSFValue($key, $val);
      }
    }
  }

  function addSFValue( $sfname, $sfvalue, $ow=true )
  {
    $sfname=strtoupper(trim($sfname));
    if($ow)
      $this->sfvalues[$sfname]=$sfvalue;
    else
    {
      if(trim($this->sfvalues[$sfname])=="")
      {
        $this->sfvalues[$sfname]=$sfvalue;
      }
    }
  }

  function getValue()
  {
    return $this->value;
  }

  function getSFValue( $sfname )
  {
    $sfname=strtoupper(trim($sfname));
    //if(!(in_array($sfname, array_keys($this->sfvalues))))
    if(!isset($this->sfvalues[$sfname]))
      return "";
    else
      return $this->sfvalues[$sfname];
  }

}//end class: IntVarValue


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Class: IntVar
//  Purpose:
//  Attributes:
//  Methods:
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


class IntVar {
  var $name; //string
  var $values; //array that contains references to IntVarValue objects which 
	       // each value of the variable
  var $ipos;
  var $vpos;
  var $subfields;  //array which will maintain a list of the variable subfields

  function IntVar($name)
  {
    $this->name=strtoupper(trim($name));
    $this->values=array();
    $this->ipos=0;
    $this->vpos=0;
    $this->subfields=array();
  }

  function vreset()
  {
    $this->vpos=0;
  }

  function hasValue($num=-1)
  {
    if($num<0)
      $num=$this->vpos;
    return ($this->values[$num]==null);
  }

  function isEmpty()
  {
    return count($this->values);
  }

  function inextValue()
  {
    $this->ipos++;
  }
  
  function vnextValue()
  {
    if($this->vpos>=$this->ipos)
      return 0;
    $this->vpos++;
    return !($this->vpos>=$this->ipos);

  }

  function vfirstSFValue($sfname)
  {
    $sfname=strtoupper(trim($sfname));
    //if(!in_array($sfname, $this->subfields))
    if(!isset($this->subfields[$sfname]))
    {
      return 0;
    }
    $this->vpos=0;
    while(!$this->values[$this->vpos]->hasSF( $sfname )) 
    {
      $this->vpos++;
      if($this->vpos>=$this->ipos) 
        return 0;
    }
    return 1;
  }

  function vnextSFValue( $sfname )
  {
    $sfname=strtoupper(trim($sfname));
    if($this->vpos>=$this->ipos)
      return 0;
    $this->vpos++;
    if($this->vpos>=$this->ipos) 
      return 0;
    while(!$this->values[$this->vpos]->hasSF( $sfname )) 
    {
      $this->vpos++;
      if($this->vpos>=$this->ipos) 
        return 0;
    }
    return 1;
  }

  function updateSF( $sfvalues )
  {
    if($sfvalues)
      foreach(array_keys($sfvalues) as $sfname)
      {
	$sfname=strtoupper(trim($sfname));
	//if(!in_array($sfname, $this->subfields))
	if(!isset($this->subfields[$sfname]))
	{
	  //array_push($this->subfields, $sfname);
	  $this->subfields[$sfname]=0;
	}
      }
  }

  function newValue( $value, $sfvalues=null)
  {
    //NOTE: if this function is called after the creation of the variable, it will
    //  produce the 0 value to be empty
    $this->inextValue();
    $this->addValue( $value, $sfvalues );
  }

  function addValue( $value, $sfvalues=null )
  {
    if(!$this->values[$this->ipos])
    {
      $this->values[$this->ipos]=new IntVarValue($value, $sfvalues);
    }
    else
    {
      $this->values[$this->ipos]->addValue($value);
      $this->values[$this->ipos]->addSFValues($sfvalues);
    }
    $this->updateSF($sfvalues);
  }

  function getValue( $num=-1 )
  {
    if($num<0)
      $num=$this->vpos;
    if($this->values[$num]==null)
      return "";
    else
    {
      $temp=$this->values[$num]->getValue();
      return $temp;
    }
  }

  function getSFValue( $sfname, $num=-1 )
  {
    $sfname=strtoupper(trim($sfname));
    //if(!in_array($sfname, $this->subfields))
    if(!isset($this->subfields[$sfname]))
      return "";
    if($num<0)
      $num=$this->vpos;
    if($this->values[$num]==null)
      return "";
    else
    {
      return $this->values[$num]->getSFValue( $sfname );
    }
  }

  function addSFValue( $sfname, $sfvalue, $ow=true )
  {
    $sfname=strtoupper(trim($sfname));
    //if(!in_array($sfname, $this->subfields))
    if(!isset($this->subfields[$sfname]))
    {
      $this->subfields[$sfname]=0;
    }
    if(!$this->values[$this->ipos])
      $this->addValue("");
    $this->values[$this->ipos]->addSFValue( $sfname, $sfvalue, $ow );
    $this->subfields[$sfname]++;
  }

  function lastValue()
  {
    if(($this->vpos+1)==$this->ipos)
      return 1;
    return 0;
  }

  function firstValue()
  {
    return(($this->ipos>0)&&($this->vpos==0));
  }

  function countValues()
  {
    return count($this->values);
  }

  function countSFValues( $sfname )
  {
    $sfname=strtoupper(trim($sfname));
    //if(!in_array($sfname, $this->subfields))
    if(!isset($this->subfields[$sfname]))
    {
      return 0;
    }
    $counter=0;
    return $this->subfields[$sfname];
  }

}//end class: IntVar


//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//  Class: Vars
//  Purpose:
//  Attributes:
//  Methods:
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class Vars {
  var $list;

  function Vars()
  {
    $this->list=array();
  }
  
  function add( $var )
  {
    if($var!=null)
    {
      $this->list[$var->name]=$var;
    }
  }

  function remove( $varname )
  {
    $varname=strtoupper(trim($varname));
    //if(in_array($varname, array_keys($this->list)))
    //{
    $this->list[$varname]=null;
    unset($this->list[$varname]);
    //}
  }

  function addVar( $varname )
  {
    $varname=strtoupper(trim($varname));
    //if(!in_array($varname, array_keys($this->list)))
    if(!isset($this->list[$varname]))
    {
      $this->list["$varname"]=new IntVar( $varname );
    }
  }

  function varExist( $varname )
  {
    $varname=strtoupper(trim($varname));
    //return in_array($varname, array_keys($this->list));
    return isset($this->list[$varname]);
  }

  function countValues( $varname, $sf="" )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      if(trim($sf)=="")
      {
        return $this->list[$varname]->countValues();
      }
      return $this->list[$varname]->countSFValues( $sf );
    }
    return 0;
  }

  function addValue( $name, $value )
  {
    $temp=explode(".", $name);
    $varname=strtoupper(trim($temp[0]));
    if(!$this->varExist( $varname ))
    {
      $this->addVar( $varname );
    }
    if(count($temp)>1)//add the value to a subfield
    {
      $sfname=strtoupper(trim($temp[1]));
      if($sfname!="")
        $this->addSFValue($varname, $sfname, $value);
    }
    else
    {
      $this->addSValue($varname, $value);
    }
  }


  function addSValue( $varname, $value, $sfvalues=null )
  {
    $varname=strtoupper(trim($varname));
    //if(!in_array($varname, array_keys($this->list)))
    if(!isset($this->list[$varname]))
    {
      return "";
    }
    else
    {
      $this->list[$varname]->addValue( $value, $sfvalues );
    }
  }
  
  function addSFValue( $varname, $sfname, $sfvalue, $ow=true )
  {
    $varname=strtoupper(trim($varname));
    //if(!in_array($varname, array_keys($this->list)))
    if(!isset($this->list[$varname]))
    {
      return "";
    }
    else
    {
      $this->list[$varname]->addSFValue( $sfname, $sfvalue, $ow );
    }
  }

  function inextValue($varname)
  {
    $varname=strtoupper(trim($varname));

    $exists=0;
    foreach(array_keys($this->list) as $k)
    {
      if("$k"==$varname) 
      {
	$exists=1;
	break;
      }
    }

    if($exists)
    {
      $this->list[$varname]->inextValue( );
    }
  }

  function getValue( $varname, $num=-1 )
  {
    $varname=strtoupper(trim($varname));
    foreach(array_keys($this->list) as $k)
    {
      if(strcmp($varname,$k)==0)
      {
        return $this->list[$varname]->getValue( $num );
      }
    }
    return "";
  }

  function getSFValue( $varname, $sfname, $num=-1 )
  {
    $varname=strtoupper(trim($varname));
    if(!isset($this->list[$varname]))
    {
      return "";
    }
    else
    {
      return $this->list[$varname]->getSFValue( $sfname, $num );
    }
  }

  function vreset( $varname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      $this->list[$varname]->vreset();
      return 1;
    }
    return 0;
  }

  function lastValue( $varname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      return $this->list[$varname]->lastValue();
    }
    return 1;
  }
  
  function firstValue( $varname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      return $this->list[$varname]->firstValue();
    }
    return 0;
  }


  function isEmpty( $varname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      return !($this->list[$varname]->isEmpty());
    }
    else
      return 1;
  }

  function vnextValue( $varname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      return $this->list[$varname]->vnextValue();
    }
    return 0;
  }

  function vnextSFValue( $varname, $sfname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
      return $this->list[$varname]->vnextSFValue( $sfname );
  }

  function vfirstSFValue( $varname, $sfname )
  {
    $varname=strtoupper(trim($varname));
    if($this->varExist($varname))
    {
      $sfname=strtoupper(trim($sfname));
      return $this->list[$varname]->vfirstSFValue($sfname);
    }
    return 0;
  }

  function debug()
  {
    print '<table width="100%" border="1">'."\n";
    print "<tr>\n";
    print "<td>Name</td><td>Values</td><td>Subfield Values</td>\n";
    print "</tr>\n";
    foreach($this->list as $name=>$var)
    {
      print "<tr>\n";
      print "<td>$name</td>\n";
      print '<td><table width="100%">'."\n";
      $cads=array();
      foreach($var->values as $value)
      {
	print "<tr><td>".$value->getValue()."</td></tr>\n";
	$cad="";
        foreach($value->sfvalues as $sfname=>$sfvalue)
        {
	  $cad.=$sfname."=".$sfvalue.",";
        }
	array_push($cads, $cad);
      }
      print "</table></td>\n";
      print '<td><table width="100%">'."\n";
      foreach($cads as $cad)
      {
	print "<tr><td>".$cad."</td></tr>\n";
      }
      print "</table></td>\n";
      print "</tr>\n";
    }
    print "</table>";

  }

}//end class: Vars

?>