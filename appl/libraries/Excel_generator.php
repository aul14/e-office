<?php

/**
 * Excel Generator for CodeIgniter
 * 
 * @author Dida Nurwanda <didanurwanda@gmail.com>
 * @link http://didanurwanda.blogspot.com 
 * 
 */
require_once dirname(__FILE__) . '/PHPExcel/PHPExcel.php';

class Excel_generator extends PHPExcel {

    /**
     * @var CI_DB_result
     */
    private $query;
    private $column = array();
    private $header = array();
    private $width = array();
    private $header_bold = TRUE;
    private $start = 1;

    /**
     * Diisi dengan query Anda
     * <pre>
     * $query = $this->db->get('users');
     * $this->excel_generator->set_query($query);
     * </pre>
     * 
     * @access public
     * @param CI_DB_result $query
     * @return Excel_generator
     */
    public function set_query(CI_DB_result $query) {
        $this->query = $query;
        return $this;
    }

    /**
     * Diisi sesuai dengan field pada table
     * <pre>
     * $this->excel_generator->set_column(array('name', 'address', 'email'));
     * </pre>
     * 
     * @access public
     * @param array $column
     * @return Excel_generator
     */
    public function set_column($column = array()) {
        $this->column = $column;
        return $this;
    }

    /**
     * Untuk mengisi header pada table excel
     * <pre>
     * $this->excel_generator->set_header(array('Name', 'Address', 'Email'));
     * </pre>
     * Jika ingin tulisannya tidak dalam bentuk bold
     * <pre>
     * $this->excel_generator->set_header(array('...'), FALSE);
     * </pre>
     * 
     * @access public
     * @param array $header
     * @param bool $set_bold
     * @return Excel_generator
     */
    public function set_header($header = array(), $set_bold = TRUE) {
        $this->header = $header;
        $this->header_bold = $set_bold;
        return $this;
    }

    /**
     * Mengubah lebar kolom
     * <pre>
     * $this->excel_generator->set_width(array(25, 30, 15));
     * </pre>     * 
     * 
     * @access public
     * @param array $width
     * @return Excel_generator
     */
    public function set_width($width = array()) {
        $this->width = $width;
        return $this;
    }

    /**
     * Mengubah baris saat memulai membuat daftar
     * <pre>
     * $this->excel_generator->start_at(5);
     * </pre>
     * 
     * @access public
     * @param int $start
     * @return Excel_generator
     */
    public function start_at($start = 1) {
        $this->start = $start;
        return $this;
    }

    /**
     * Untuk menghasilkan data excel
     * 
     * @access public
     * @return Excel_generator
     */
    public function generate() {
        $start = $this->start;
        if (count($this->header) > 0) {
            $abj = 1;
            foreach ($this->header as $row) {
                $this->getActiveSheet()->setCellValue($this->columnName($abj) . $this->start, $row);
                if ($this->header_bold) {
                    $this->getActiveSheet()->getStyle($this->columnName($abj) . $this->start)->getFont()->setBold(TRUE);
                }
                
                $abj++;
            }
            
            $start = $this->start + 1;
        }

        foreach ($this->query->result_array() as $result_db) {
            $index = 1;
            foreach ($this->column as $row) {
                if (count($this->width) > 0) {
                    $this->getActiveSheet()->getColumnDimension($this->columnName($index))->setWidth($this->width[$index - 1]);
                }

                $this->getActiveSheet()->setCellValue($this->columnName($index) . $start, $result_db[$row]);
                $index++;
            }
            
            $start++;
        }
        
        return $this;
    }

    private function columnName($index) {
        --$index;
        if ($index >= 0 && $index < 26)
            return chr(ord('A') + $index);
        else if ($index > 25)
            return ($this->columnName($index / 26)) . ($this->columnName($index % 26 + 1));
        else
            show_error("Invalid Column # " . ($index + 1));
    }

    /**
     * Untuk membuat file excel
     * 
     * @param string $filename
     * @param string $writerType
     * @param string $mimes
     */
    private function writeToFile($filename = 'doc', $writerType = 'Excel5', $mimes = 'application/vnd.ms-excel') {
        $this->generate();
        header("Content-Type: $mimes");
        //header("Content-Disposition: attachment;filename=\"$filename\"");
        header("Content-Disposition: attachment;filename='".$filename."'");
        header("Cache-Control: max-age=0");

        // header("Pragma: public");
        // header("Expires: 0");
        // header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        // header("Content-Type: application/force-download");
        // header("Content-Type: application/octet-stream");
        // header("Content-Type: application/download");;
        // header("Content-Disposition: attachment; filename='".$filename."'");
        //header("Content-Transfer-Encoding: binary ");
        $objWriter = PHPExcel_IOFactory::createWriter($this, $writerType);
        $objWriter->save('php://output');
        //$objWriter->save($filename);
    }

    /**
     * @param string $filename
     */
    public function exportTo2003($filename = 'doc') {
        $this->writeToFile($filename . '.xls');
    }

    /**
     * @param string $filename
     */
    public function exportTo2007($filename = 'doc') {
        $this->writeToFile($filename . '.xlsx', 'Excel2007', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

}