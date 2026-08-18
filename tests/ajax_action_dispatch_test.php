<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_simplequiz2;

/**
 * Tests AJAX action allowlisting.
 *
 * @package    mod_simplequiz2
 * @category   test
 * @copyright  2026 Edunao SAS (contact@edunao.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_simplequiz2\mod_api
 */
final class ajax_action_dispatch_test extends \advanced_testcase {
    /**
     * Set up each test case.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Build a controller that captures send() instead of exiting.
     *
     * @return mod_api
     */
    private function create_test_controller(): mod_api {
        return new class extends mod_api {
            /**
             * Skip HTTP bootstrap; only exercise dispatch helpers.
             */
            public function __construct() {
                $this->db = database_interface::get_instance();
            }

            /**
             * Capture the HTTP response instead of exiting.
             *
             * @param int $httpstatus HTTP status code.
             * @param string $message Response message.
             * @param array $payload Extra payload.
             * @return never
             */
            protected function send(int $httpstatus, string $message, array $payload = []): void {
                throw new \RuntimeException(json_encode([
                    'status' => $httpstatus,
                    'message' => $message,
                ], JSON_THROW_ON_ERROR));
            }

            /**
             * Allowed handler stub.
             */
            protected function check_question(): void {
            }

            /**
             * Allowed handler stub.
             */
            protected function get_attempt_results(): void {
            }
        };
    }

    /**
     * Invoke private prepare_action via reflection.
     *
     * @param mod_api $controller Controller under test.
     * @param string|null $action Action name.
     */
    private function prepare_action(mod_api $controller, ?string $action): void {
        $method = new \ReflectionMethod(mod_api::class, 'prepare_action');
        $method->setAccessible(true);
        $method->invoke($controller, $action);
    }

    /**
     * Read private action property.
     *
     * @param mod_api $controller Controller under test.
     * @return string
     */
    private function get_action(mod_api $controller): string {
        $property = new \ReflectionProperty(mod_api::class, 'action');
        $property->setAccessible(true);
        return (string) $property->getValue($controller);
    }

    /**
     * Decode a RuntimeException thrown by the test send() override.
     *
     * @param \RuntimeException $exception Captured send() exception.
     * @return array{status: int, message: string}
     */
    private function decode_send(\RuntimeException $exception): array {
        $data = json_decode($exception->getMessage(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('message', $data);
        return $data;
    }

    /**
     * action=run must be rejected with HTTP 405 without recursive dispatch.
     */
    public function test_prepare_action_rejects_run(): void {
        $controller = $this->create_test_controller();

        try {
            $this->prepare_action($controller, 'run');
            $this->fail('Expected HTTP 405 for action=run');
        } catch (\RuntimeException $exception) {
            $data = $this->decode_send($exception);
            $this->assertSame(405, $data['status']);
            $this->assertStringContainsString('run', $data['message']);
        }

        $this->assertSame('', $this->get_action($controller));
    }

    /**
     * Other public infrastructure method names must also be rejected.
     */
    public function test_prepare_action_rejects_send(): void {
        $controller = $this->create_test_controller();

        try {
            $this->prepare_action($controller, 'send');
            $this->fail('Expected HTTP 405 for action=send');
        } catch (\RuntimeException $exception) {
            $data = $this->decode_send($exception);
            $this->assertSame(405, $data['status']);
        }
    }

    /**
     * Allowed actions are accepted for dispatch.
     */
    public function test_prepare_action_allows_check_question(): void {
        $controller = $this->create_test_controller();
        $this->prepare_action($controller, 'check_question');
        $this->assertSame('check_question', $this->get_action($controller));
    }

    /**
     * run() must refuse a planted disallowed action without recursion.
     */
    public function test_run_rejects_disallowed_action_without_recursion(): void {
        $controller = $this->create_test_controller();
        $property = new \ReflectionProperty(mod_api::class, 'action');
        $property->setAccessible(true);
        $property->setValue($controller, 'run');

        try {
            $controller->run();
            $this->fail('Expected HTTP 405 when run() is invoked with action=run');
        } catch (\RuntimeException $exception) {
            $data = $this->decode_send($exception);
            $this->assertSame(405, $data['status']);
        }
    }
}
