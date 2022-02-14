<?php

class DataStructure
{

  public static function unique_multidim_array($array, $key)
  {
    $temp_array = array();
    $i = 0;
    $key_array = array();

    foreach ($array as $val) {
      if (!empty($val[$key]) && !in_array($val[$key], $key_array)) {
        $key_array[$i] = $val[$key];
        $temp_array[$i] = $val;
      }
      $i++;
    }
    return $temp_array;
  }


  public static function to2DArray($data, $key, $idName = NULL)
  {
    $ret = [];
    $counter = 1;
    foreach ($data as $d) {
      if (!empty($idName)) $ret[] = [$key => $d, $idName => $counter++];
      else $ret[] = [$key => $d];
    }
    return $ret;
  }

  public static function getNewAndUpdates($new, $existing)
  {
    return [
      'new' => array_diff_key($new, $existing),
      'updates' => array_intersect_key($new, $existing),
      'removed' => array_diff_key($existing, $new),
    ];
  }

  public static function flatten($arr, $key = False)
  {
    $ret = [];
    foreach ($arr as $k => $a) {
      foreach ($a as $aa) {
        if ($key) {
          $ret[$k] = $aa;
        } else {
          $ret[] = $aa;
        }
      }
    }
    return $ret;
  }

  public static function transform($arr, $fields)
  {
    $ret = [];
    foreach ($arr as $k => $a) {
      $ret[$k] = $a;
      foreach ($fields as $sk => $tk) {
        $ret[$k][$tk] = $a[$sk];
        unset($ret[$k][$sk]);
      }
    }
    return $ret;
  }

  public static function merge($target, $source, $key, $fields)
  {
    $ret = [];
    foreach ($target as $tk => $tv) {
      if (isset($source[$tv[$key]])) {
        $src = $source[$tv[$key]];
        $ret[$tk] = $target[$tk];
        foreach ($fields as $fs => $ft) {
          $ret[$tk][$ft] = $src[$fs];
        }
      }
    }
    return $ret;
  }

  public static function count($arr, $val, $key)
  {
    $count = 0;
    foreach ($arr as $a) {
      if ($a[$key] == $val) {
        $count++;
      }
    }
    return $count;
  }

  public static function broadcast($arr, $vals, $keys, $assoc = TRUE)
  {
    for ($i = 0; $i < count($vals); $i++) {
      foreach ($arr as $k => $a) {
        $arr[$k][$keys[$i]] = $vals[$i];
      }
    }
    if (!$assoc) $arr = DataStructure::associativeToArray($arr);
    return $arr;
  }



  public static function associativeToArray($arr)
  {
    $ret = array();
    if ($arr == NULL) return $ret;
    foreach ($arr as $a) {
      $ret[] = $a;
    }
    return $ret;
  }

  public static function keyValue($arr, $key, $value = NULL)
  {
    $ret = array();
    if ($arr == NULL) return $ret;
    foreach ($arr as $a) {
      $ret[$a[$key]] = $value != NULL ? $a[$value] : $a;
    }
    return $ret;
  }

  // arr: [{a: 'gg', b: 'wp'}, {a: 'ee', b: 'tt'}]
  // key: a
  // output: ['gg', 'ee']
  public static function toOneDimension($arr, $key, $object = FALSE)
  {
    $ret = array();
    if ($arr == NULL) return $ret;
    foreach ($arr as $a) {
      if ($object) {
        $ret[$a[$key]] = $a[$key];
      } else {
        $ret[] = $a[$key];
      }
    }
    return $ret;
  }

  public static function slice($arr, $fields, $empty = FALSE)
  {
    $ret = array();
    if ($fields == NULL) return $ret;

    foreach ($fields as $f) {
      if ((isset($arr[$f]) || array_key_exists($f, $arr)) && (!$empty || !empty($arr[$f])))
        $ret[$f] = $arr[$f];
    }
    return $ret;
  }

  public static function slice2D($arr, $fields)
  {
    $ret = [];
    foreach ($arr as $k => $a) {
      $ret[$k] = DataStructure::slice($a, $fields);
    }
    return $ret;
  }

  public static function selfGrouping($arr, $parentForeign, $childName)
  {
    $ret = array();
    foreach ($arr as $a) {
      if ($a[$parentForeign] == null) {
        $ret[$a['id']] = $a;
        $ret[$a['id']][$childName] = array();
      }
    }

    foreach ($arr as $a) {
      if ($a[$parentForeign] != null) {
        $ret[$a[$parentForeign]][$childName][] = $a;
      }
    }

    return $ret;
  }

  public static function groupByRecursive2($arr, $columns, $childKeys, $parentFields, $childNames, $assoc = TRUE)
  {
    if (count($columns) == 0) {
      return DataStructure::slice2D($arr, $parentFields[0]);
    }
    $childName = $childNames[0];
    $ret = DataStructure::groupBy2($arr, array_shift($columns), array_shift($childKeys), array_shift($parentFields), array_shift($childNames));
    $ret = !$assoc ? DataStructure::associativeToArray($ret) : $ret;
    foreach ($ret as $k => $r) {
      $ret[$k][$childName] = DataStructure::groupByRecursive2(DataStructure::flatten($r[$childName], count($columns) == 0 || !$assoc), $columns, $childKeys, $parentFields, $childNames, $assoc);
    }
    return $ret;
  }

  public static function renderJurnal($arr, $columns, $childKeys, $parentFields, $childNames, $assoc = TRUE)
  {
    if (count($columns) == 0) {
      return DataStructure::slice2D($arr, $parentFields[0]);
    }
    $childName = $childNames[0];
    $ret = DataStructure::groupBy2($arr, array_shift($columns), array_shift($childKeys), array_shift($parentFields), array_shift($childNames));
    $ret = !$assoc ? DataStructure::associativeToArray($ret) : $ret;
    foreach ($ret as $k => $r) {
      $ret[$k][$childName] = DataStructure::groupByRecursive2(DataStructure::flatten($r[$childName], count($columns) == 0 || !$assoc), $columns, $childKeys, $parentFields, $childNames, $assoc);
    }
    foreach ($ret as $k2 => $r2) {
      // foreach()
      $sort_col = array();

      $child = DataStructure::associativeToArray($r2['children']);
      $child = DataStructure::array_sort_by_column($child, 'type');
      $ret[$k2]['children'] = $child;
      // foreach ($r2['children'] as $key => $row) {
      //   // $sort_col[$key] = $row[$col];
      // }
    }
    // echo json_encode($ret);
    // die();
    return $ret;
  }

  public static function renderCF($arr, $columns, $childKeys, $parentFields, $childNames, $assoc = TRUE)
  {
    if (count($columns) == 0) {
      return DataStructure::slice2D($arr, $parentFields[0]);
    }
    $childName = $childNames[0];
    $cek = array();
    $ret = array();
    // $ret = DataStructure::groupBy2($arr, array_shift($columns), array_shift($childKeys), array_shift($parentFields), array_shift($childNames));
    // $ret = !$assoc ? DataStructure::associativeToArray($ret) : $ret;
    foreach ($arr as $r) {
      if (substr($r['head_number'], 0, 3) == '101') {
        if (!isset($cek[$r['parent_id']][0])) {
          $cek[$r['parent_id']]['0'] = 0;
          $cek[$r['parent_id']]['1'] = 0;
          if ($r['type'] == 0)
            $cek[$r['parent_id']]['0'] = $r['amount'];
          else
            $cek[$r['parent_id']]['1'] = $r['amount'];
        } else {
          if ($r['type'] == 0)
            $cek[$r['parent_id']]['0'] = $cek[$r['parent_id']]['0'] + $r['amount'];
          else
            $cek[$r['parent_id']]['1'] = $cek[$r['parent_id']]['1'] + $r['amount'];
        }
      }
    }
    foreach ($cek as $key => $r2) {
      if ($r2[0] > $r2[1])
        $cek[$key]['get_pos'] = 1;
      else
        $cek[$key]['get_pos'] = 0;
    }

    $datas['out_general'] = 0;
    $datas['out_pajak'] = 0;
    $datas['out_usaha'] = 0;

    $datas['in_bank'] = 0;
    $datas['in_dll'] = 0;
    $datas['in_usaha'] = 0;
    $datas['piutang_bank'] = 0;
    //kegiatan investasi
    $datas['inves_pinjaman'] = 0;
    $i = 0;
    $datas['tmp_total'] = 0;
    $test = 0;
    foreach ($arr as $key => $r3) {
      if ($r3['type'] == $cek[$r3['parent_id']]['get_pos']) {

        $datas['tmp_total'] = $datas['tmp_total'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
        if (substr($r3['head_number'], 0, 3) != '101') {
          $test = $test + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok

          if (substr($r3['head_number'], 0, 3) == '501') {
            $datas['out_general'] = $datas['out_general'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
            // echo $i . '. ' . number_format($r3['amount']) . ' :General: ' . number_format($datas['out_general']) . '<br>';
            // $i++;
          } else if (substr($r3['head_number'], 0, 3) == '502') { // done out usaha
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
            $datas['out_usaha'] = $datas['out_usaha'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
          } else if (substr($r3['head_number'], 0, 3) == '403') { // done pend lain 
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
            $datas['in_dll'] = $datas['in_dll'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
          } else if (substr($r3['head_number'], 0, 3) == '503' or substr($r3['head_number'], 0, 3) == '201') { // done output pajak
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
            $datas['out_pajak'] = $datas['out_pajak'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
          } else if (substr($r3['head_number'], 0, 3) == '402') { //done pend bank
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
            $datas['in_bank'] = $datas['in_bank'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
          } else if (substr($r3['head_number'], 0, 3) == '103' or substr($r3['head_number'], 0, 3) == '401') {  //done pend usaha lewat piutang dan langsung
            $datas['in_usaha'] = $datas['in_usaha'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
          } else if (substr($r3['head_number'], 0, 3) == '104') {
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
            $datas['piutang_bank'] = $datas['piutang_bank'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
          } else if (substr($r3['head_number'], 0, 3) == '203') {
            // echo $i . '. ' . number_format($r3['amount']) . ' :: ' . number_format($datas['in_usaha']) . '<br>';
            // $i++;
            $datas['inves_pinjaman'] = $datas['inves_pinjaman'] + ($r3['type'] == 1 ?  $r3['amount'] : -$r3['amount']); //ok
          };
        }
      }
    }

    // echo 't' . $test;
    $total['inves'] =
      $datas['inves_pinjaman'];
    $total['operasi'] =
      $datas['out_general'] +
      $datas['in_usaha'] +
      $datas['out_usaha'] +
      $datas['in_dll'] +
      $datas['piutang_bank'] +
      $datas['in_bank'] +
      $datas['out_pajak'];

    $total['all'] = $total['operasi'] + $total['inves'];
    $datas['total'] = $total;
    return $datas;
    // return array('data' => $res, 'jenis' => $datas);

    // echo json_encode($cek);
    // echo json_encode($datas);
    // die();
    foreach ($ret as $k2 => $r2) {
      // foreach()
      $sort_col = array();

      $child = DataStructure::associativeToArray($r2['children']);
      $child = DataStructure::array_sort_by_column($child, 'head_number');
      $ret[$k2]['children'] = $child;
      // foreach ($r2['children'] as $key => $row) {
      //   // $sort_col[$key] = $row[$col];
      // }
    }
    // echo json_encode($ret);
    // die();
    return $ret;
  }
  public static  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
  {
    // var_dump($arr);
    // die();
    $sort_col = array();
    foreach ($arr as $key => $row) {
      $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
    return $arr;
  }


  public static function TreeAccounts($arr, $assoc = TRUE)
  {
    $res = array();
    foreach ($arr as $k) {
      if (substr($k['head_number'], 1, 6) == '00000') {
        $res[substr($k['head_number'], 0, 1)] = array('head_number' => substr($k['head_number'], 0, 1), 'nature' => $k['nature'], 'name' => $k['name']);
        $res[substr($k['head_number'], 0, 1)]['children'] = array();
      } else
      if (substr($k['head_number'], 3, 3) == '000') {
        $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)] =  array('head_number' => substr($k['head_number'], 1, 2), 'name' => $k['name'], 'type' => $k['type'], 'nature' => $k['nature'], 'children' => array());
      } else {
        $res[substr($k['head_number'], 0, 1)]['children'][substr($k['head_number'], 1, 2)]['children'][substr($k['head_number'], 3, 3)] =  array('head_number' =>  substr($k['head_number'], 3, 3), 'name' => $k['name'], 'id_head' => $k['id']);
      }
    }
    return $res;
  }

  public static function jstreeStructure($arr, $columns, $childKeys, $parentFields, $childNames, $assoc = TRUE)
  {
    if (count($columns) == 0) {
      return DataStructure::slice2D($arr, $parentFields[0]);
    }
    $childName = $childNames[0];
    $ret = DataStructure::groupBy2($arr, array_shift($columns), array_shift($childKeys), array_shift($parentFields), array_shift($childNames));
    $ret = !$assoc ? DataStructure::associativeToArray($ret) : $ret;
    foreach ($ret as $k => $r) {
      $ret[$k][$childName] = DataStructure::groupByRecursive2(DataStructure::flatten($r[$childName], count($columns) == 0 || !$assoc), $columns, $childKeys, $parentFields, $childNames, $assoc);
    }
    $jstree = array();
    $i = 0;
    foreach ($ret as $k) {
      $data = array('text' => $k['name'], 'id' => 'parent_' . $k['parent_id'], 'state' => array('opened' => true));
      $data['children'] = array();
      // $jstree[$i] = $data;
      $l = 0;
      foreach ($k['children'] as $l) {
        $tmp = array('text' => $l['sub_name'], 'id' => $l['page_id'],  'state' => array('opened' => false), 'li_attr' => array('class' => 'custom_row'), 'children' => [
          array('text' => 'View', 'id' => 'v_' . $l['page_id'], 'state' => array('selected' => $l['view'] == 1 ? true : false, 'opened' => false)),
          array('text' => 'Create', 'id' => 'c_' . $l['page_id'], 'state' => array('selected' => $l['hk_create'] == 1 ? true : false, 'opened' => false)),
          array('text' => 'Update', 'id' => 'u_' . $l['page_id'], 'state' => array('selected' => $l['hk_update'] == 1 ? true : false, 'opened' => false)),
          array('text' => 'Delete', 'id' => 'd_' . $l['page_id'], 'state' => array('selected' => $l['hk_delete'] == 1 ? true : false, 'opened' => false))
        ]);
        array_push($data['children'], $tmp);
        // echo json_encode($data);
        // die();
        $l++;
      }
      array_push($jstree, $data);
    }



    // echo json_encode($jstree);
    // die();
    return $jstree;
  }
  public static function CashFlow($arr)
  {
    $res = array();
    foreach ($arr as $k) {
      // echo json_encode($k);
      // if($k)

      if (substr($k['head_number'], 0, 3) == '101') {
        if (empty($res[$k['parent_id']]['kas'])) {
          if ($k['type'] == 0) {
            $res[$k['parent_id']]['kas'] = $k['amount'];
          } else {
            $res[$k['parent_id']]['kas'] = -$k['amount'];
          }
          // if ($k)
        } else {
          if ($k['type'] == 0) {
            $res[$k['parent_id']]['kas'] = $res[$k['parent_id']]['kas'] + $k['amount'];
          } else {
            $res[$k['parent_id']]['kas'] = $res[$k['parent_id']]['kas'] - $k['amount'];
          }
        }
        // $kas = true;
      }

      // die();
      // if ($kas)
      //   if (empty($res[$k['parent_id']]['kas'])) {
      //     if ($k['type'] == 0) {
      //       $res[$k['parent_id']]['kas'] = $k['amount'];
      //     } else {
      //       $res[$k['parent_id']]['kas'] = -$k['amount'];
      //     }
      //     // if ($k)
      //   } else {
      //     if ($k['type'] == 0) {
      //       $res[$k['parent_id']]['kas'] = $res[$k['parent_id']]['kas'] + $k['amount'];
      //     } else {
      //       $res[$k['parent_id']]['kas'] = $res[$k['parent_id']]['kas'] - $k['amount'];
      //     }
      //   }



      $res[$k['parent_id']]['child'][] = $k['sub_id'];
    }
    echo json_encode($res);
    die();
  }
  public static function detectCashFlow($arr)
  {
    $datas['out_general'] = 0;
    $datas['out_pajak'] = 0;
    $datas['out_usaha'] = 0;

    $datas['in_bank'] = 0;
    $datas['in_dll'] = 0;
    $datas['in_usaha'] = 0;
    $datas['piutang_bank'] = 0;
    //kegiatan investasi
    $datas['inves_pinjaman'] = 0;

    $res = array();
    foreach ($arr as $k) {
      $res[$k['id']] = $k;
      if (substr($k['h2'], 0, 3) == '101') {
      } else if (substr($k['h2'], 0, 3) == '501') {
        // echo json_encode($k);
        // die();
        // $res[$k['id']]['jenis'][] = 'out_general';

        $datas['out_general'] = $datas['out_general'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '502') { // done out usaha
        $datas['out_usaha'] = $datas['out_usaha'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '403') { // done pend lain 
        $datas['in_dll'] = $datas['in_dll'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '503') { // done output pajak
        $datas['out_pajak'] = $datas['out_pajak'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '402') { //done pend bank
        $datas['in_bank'] = $datas['in_bank'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '103' or substr($k['h2'], 0, 3) == '401') {  //done pend usaha lewat piutang dan langsung
        $datas['in_usaha'] = $datas['in_usaha'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '104') {
        $datas['piutang_bank'] = $datas['piutang_bank'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      } else if (substr($k['h2'], 0, 3) == '203') {
        $datas['inves_pinjaman'] = $datas['inves_pinjaman'] + ($k['type'] == 0 ?  $k['amount'] : -$k['amount']); //ok
      };
    }

    $total['inves'] =
      $datas['inves_pinjaman'];
    $total['operasi'] =
      $datas['out_general'] +
      $datas['in_usaha'] +
      $datas['out_usaha'] +
      $datas['in_dll'] +
      $datas['piutang_bank'] +
      $datas['in_bank'] +
      $datas['out_pajak'];

    $datas['total'] = $total;

    return array('data' => $res, 'jenis' => $datas);
  }
  // arr: [{a: 'gg', b: 'wp'}, {a: 'gg', b: 'tt'}, {a: 'yy', b: 'oo'}]
  // column: a
  // output: ['gg': [{a: 'gg', b: 'wp'}, {a: 'gg', b: 'tt'}], 'yy': [{a: 'yy', b: 'oo'}]]
  public static function groupBy2($arr, $column, $childKey, $parentField, $childName)
  {

    $ret = array();
    foreach ($arr as $a) {
      $groupKey = $a[$column];
      if (!isset($ret[$groupKey])) {
        $ret[$groupKey] = DataStructure::slice($a, $parentField);
        $ret[$groupKey][$childName] = [];
      }
      if ($a[$childKey] == null) continue;
      if (!isset($ret[$groupKey][$childName][$a[$childKey]])) {
        $ret[$groupKey][$childName][$a[$childKey]] = [];
      }
      $ret[$groupKey][$childName][$a[$childKey]][] = $a;
    }
    return $ret;
  }

  public static function groupJstree($arr, $column, $childKey, $parentField, $childName)
  {

    $ret = array();
    $i = 0;
    foreach ($arr as $a) {
      $groupKey = $a[$column];
      if (!isset($ret[$groupKey])) {

        $ret[$i] = DataStructure::slice($a, $parentField);
        $ret[$i][$childName] = [];
      }
      if ($a[$childKey] == null) continue;
      if (!isset($ret[$groupKey][$childName][$a[$childKey]])) {
        $ret[$groupKey][$childName][$a[$childKey]] = [];
      }
      $ret[$groupKey][$childName][$a[$childKey]][] = $a;
      echo json_encode($ret);
      die();
    }

    return $ret;
  }

  public static function groupByRecursive($arr, $columns, $childKey)
  {
    if (count($columns) == 0) return $arr;
    $ret = DataStructure::groupBy($arr, array_shift($columns), count($columns) == 0 ? $childKey : NULL);
    foreach ($ret as $k => $r) {
      $ret[$k] = DataStructure::groupByRecursive($r, $columns, $childKey);
    }
    return $ret;
  }

  // arr: [{a: 'gg', b: 'wp'}, {a: 'gg', b: 'tt'}, {a: 'yy', b: 'oo'}]
  // column: a
  // output: ['gg': [{a: 'gg', b: 'wp'}, {a: 'gg', b: 'tt'}], 'yy': [{a: 'yy', b: 'oo'}]]
  public static function groupBy($arr, $column, $childKey = NULL, $childCol = NULL)
  {
    $ret = array();
    foreach ($arr as $a) {
      $groupName = $a[$column];
      if (!isset($ret[$groupName])) {
        $ret[$groupName] = array();
      }
      if ($childKey != NULL) {
        $ret[$groupName][$a[$childKey]] = !empty($childCol) ? $a[$childCol] : $a;
      } else {
        $ret[$groupName][] = $a;
      }
    }
    return $ret;
  }

  public static function groupAndFlatten($arr, $parentKey, $childKey)
  {
    $ret = array();
    foreach ($arr as $a) {
      $key = $a[$parentKey];
      if (!isset($ret[$key])) {
        $ret[$key] = array();
      }
      $ret[$key][$a[$childKey]] = $a[$childKey];
    }
    return $ret;
  }

  public static function filter($arr, $cond)
  {
    $ret = [];
    foreach ($arr as $k => $a) {
      $satisfy = true;
      foreach ($cond as $field => $value) {
        if (!isset($a[$field]) || $a[$field] != $value) $satisfy = $satisfy && false;
      }
      if ($satisfy == true) $ret[$k] = $a;
    }
    return $ret;
  }

  // arr: [{a: '###', b: 'wp'}, {a: 'gg', b: '###'}, {a: 'yy', b: '###'}]
  // value: ###
  // output: [{a: 'gg'}, {b: 'tt'}, {a: 'yy''}]
  public static function deleteColumnWhere($arr = array(), $value)
  {
    $ret = array();
    foreach ($arr as $a) {
      $item = array();
      foreach ($a as $cname => $cvalue) {
        if ($cvalue != $value) {
          $item[$cname] = $cvalue;
        }
      }
      $ret[] = $item;
    }
    return $ret;
  }
}
